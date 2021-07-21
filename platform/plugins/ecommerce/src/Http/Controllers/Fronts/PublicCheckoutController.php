<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Http\Requests\PayPalPaymentCallbackRequest;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
use Botble\Payment\Services\Gateways\StripePaymentService;
use Carbon\Carbon;
use Cart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use OrderHelper;
use Throwable;

class PublicCheckoutController
{
    /**
     * @var TaxInterface
     */
    protected $taxRepository;

    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var OrderProductInterface
     */
    protected $orderProductRepository;

    /**
     * @var OrderAddressInterface
     */
    protected $orderAddressRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var ShippingInterface
     */
    protected $shippingRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var DiscountInterface
     */
    protected $discountRepository;

    /**
     * PublicCheckoutController constructor.
     * @param TaxInterface $taxRepository
     * @param OrderInterface $orderRepository
     * @param OrderProductInterface $orderProductRepository
     * @param OrderAddressInterface $orderAddressRepository
     * @param AddressInterface $addressRepository
     * @param CustomerInterface $customerRepository
     * @param ShippingInterface $shippingRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     * @param ProductInterface $productRepository
     * @param DiscountInterface $discountRepository
     */
    public function __construct(
        TaxInterface $taxRepository,
        OrderInterface $orderRepository,
        OrderProductInterface $orderProductRepository,
        OrderAddressInterface $orderAddressRepository,
        AddressInterface $addressRepository,
        CustomerInterface $customerRepository,
        ShippingInterface $shippingRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ProductInterface $productRepository,
        DiscountInterface $discountRepository
    ) {
        $this->taxRepository = $taxRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->shippingRepository = $shippingRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->productRepository = $productRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @param string $token
     * @param BaseHttpResponse $response
     * @param HandleApplyPromotionsService $applyPromotionsService
     * @return BaseHttpResponse|Application|Factory|View
     */
    public function getCheckout(
        $token,
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);
            if (!$order) {
                return $response->setNextUrl(url('/'));
            }
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        $weight = 0;
        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = $this->productRepository->findById($cartItem->id);
            if ($product && $product->weight) {
                $weight += $product->weight;
            }
        }

        $weight = $weight ? $weight : 0.1;

        $promotionDiscountAmount = $applyPromotionsService->execute($token);
        $couponDiscountAmount = 0;
        if (session()->has('applied_coupon_code')) {
            $couponDiscountAmount = Arr::get($sessionCheckoutData, 'coupon_discount_amount', 0);
        }

        $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount;
        $orderTotal = $orderTotal > 0 ? $orderTotal : 0;

        $shippingData = [
            'address'     => Arr::get($sessionCheckoutData, 'address'),
            'country'     => Arr::get($sessionCheckoutData, 'country'),
            'state'       => Arr::get($sessionCheckoutData, 'state'),
            'city'        => Arr::get($sessionCheckoutData, 'city'),
            'weight'      => $weight,
            'order_total' => $orderTotal,
        ];

        $shipping = $shippingFeeService->execute($shippingData);

        foreach ($shipping as $key => &$shipItem) {
            if (get_shipping_setting('free_ship', $key)) {
                foreach ($shipItem as &$subShippingItem) {
                    Arr::set($subShippingItem, 'price', 0);
                }
            }
        }

        $defaultShippingMethod = $request->input('shipping_method',
            old('shipping_method',
                Arr::get($sessionCheckoutData, 'shipping_method', Arr::first(array_keys($shipping)))));
        $defaultShippingOption = null;
        if (!empty($shipping)) {
            $defaultShippingOption = Arr::first(array_keys(Arr::first($shipping)));
            $defaultShippingOption = $request->input('shipping_option',
                old('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option', $defaultShippingOption)));
        }
        $shippingAmount = Arr::get($shipping, $defaultShippingMethod . '.' . $defaultShippingOption . '.price', 0);

        Arr::set($sessionCheckoutData, 'shipping_method', $defaultShippingMethod);
        Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
        Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);
        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        if (session()->has('applied_coupon_code')) {
            if (!$request->input('applied_coupon')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionCheckoutData);
                if (empty($discount)) {
                    $removeCouponService->execute($token);
                } else {
                    $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_ship') ? 0 : $shippingAmount;
                }
            } else {
                $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_ship') ? 0 : $shippingAmount;
            }
        }

        return view('plugins/ecommerce::orders.checkout', compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData'
        ));
    }

    /**
     * @param string $token
     * @param SaveCheckoutInformationRequest $request
     * @param BaseHttpResponse $response
     * @param HandleApplyCouponService $applyCouponService
     * @param HandleRemoveCouponService $removeCouponService
     * @return BaseHttpResponse
     */
    public function postSaveInformation(
        $token,
        SaveCheckoutInformationRequest $request,
        BaseHttpResponse $response,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);
            if (!$order) {
                return $response->setNextUrl(url('/'));
            }
        }

        $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

        $address = null;
        if ($request->input('address.address_id') && $request->input('address.address_id') !== 'new') {
            $address = $this->addressRepository->findById($request->input('address.address_id'));
            if (!empty($address)) {
                $sessionData = [
                    'address_id' => $address->id,
                    'name'       => $address->name,
                    'phone'      => $address->phone,
                    'email'      => $address->email,
                    'country'    => $address->country,
                    'state'      => $address->state,
                    'city'       => $address->city,
                ];
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionData);
        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute($token);
            }
        }

        if (!isset($sessionData['created_account']) && $request->input('create_account') == 1) {
            $this->customerRepository->createOrUpdate([
                'name'     => $request->input('address.name'),
                'email'    => $request->input('address.email'),
                'password' => bcrypt($request->input('password')),
            ]);

            auth('customer')->attempt([
                'email'    => $request->input('address.email'),
                'password' => $request->input('password'),
            ], true);

            $sessionData['created_account'] = true;

            OrderHelper::setOrderSessionData($token, $sessionData);
        }

        if (!isset($sessionData['created_order'])) {
            $currentUserId = 0;
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->user()->getAuthIdentifier();
            }

            $request->merge([
                'amount'          => Cart::instance('cart')->rawTotal(),
                'currency_id'     => get_application_currency_id(),
                'user_id'         => $currentUserId,
                'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
                'shipping_option' => $request->input('shipping_option'),
                'shipping_amount' => 0,
                'tax_amount'      => Cart::instance('cart')->rawTax(),
                'sub_total'       => Cart::instance('cart')->rawSubTotal(),
                'coupon_code'     => session()->get('applied_coupon_code'),
                'discount_amount' => 0,
                'status'          => OrderStatusEnum::PENDING,
                'is_finished'     => false,
                'token'           => $token,
            ]);

            $order = $this->orderRepository->createOrUpdate($request->input());

            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->id;

            OrderHelper::setOrderSessionData($token, $sessionData);
        }


        $address = null;
        if (Arr::get($sessionData, 'address_id') && Arr::get($sessionData, 'address_id') !== 'new') {
            $address = $this->addressRepository->findById(Arr::get($sessionData, 'address_id'));
        }

        if (!empty($address)) {
            $addressData = [
                'name'     => $address->name,
                'phone'    => $address->phone,
                'email'    => $address->email,
                'country'  => $address->country,
                'state'    => $address->state,
                'city'     => $address->city,
                'address'  => $address->address,
                'order_id' => $order->id,
            ];
        } else {
            $addressData = array_merge(['order_id' => $sessionData['created_order_id']], $sessionData);
        }

        if (!isset($sessionData['created_order_address'])) {
            $createdOrderAddress = $this->orderAddressRepository->create($addressData);
            $sessionData['created_order_address'] = true;
            $sessionData['created_order_address_id'] = $createdOrderAddress->id;

            OrderHelper::setOrderSessionData($token, $sessionData);
        } elseif (!empty($sessionData['created_order_address_id'])) {
            $this->orderAddressRepository->createOrUpdate($addressData, ['id' => $sessionData['created_order_address_id']]);
        }

        if (!isset($sessionData['created_order_product'])) {
            $weight = 0;
            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);
                if ($product) {
                    if ($product->weight) {
                        $weight += $product->weight;
                    }
                }
            }

            $weight = $weight > 0.1 ? $weight : 0.1;

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $data = [
                    'order_id'     => $order->id,
                    'product_id'   => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'qty'          => $cartItem->qty,
                    'weight'       => $weight,
                    'price'        => $cartItem->price,
                    'tax_amount'   => $cartItem->taxRate / 100 * $cartItem->price,
                    'options'      => [],
                ];
                if ($cartItem->options->product_version) {
                    $data['options']['product_version_id'] = $cartItem->options->product_version;
                    $data['options']['version_info'] = $cartItem->options->version_info;
                }

                $this->orderProductRepository->create($data);

                $this->productRepository
                    ->getModel()
                    ->where('id', $cartItem->id)
                    ->where('with_storehouse_management', 1)
                    ->where('quantity', '>', 0)
                    ->decrement('quantity', $cartItem->qty);
            }

            $sessionData['created_order_product'] = true;

            OrderHelper::setOrderSessionData($token, $sessionData);
        }

        return $response->setData($sessionData);
    }

    /**
     * @param string $token
     * @param CheckoutRequest $request
     * @param PayPalPaymentService $palPaymentService
     * @param StripePaymentService $stripePaymentService
     * @param BaseHttpResponse $response
     * @param HandleShippingFeeService $shippingFeeService
     * @param HandleApplyCouponService $applyCouponService
     * @param HandleRemoveCouponService $removeCouponService
     * @param HandleApplyPromotionsService $handleApplyPromotionsService
     * @return mixed
     * @throws Throwable
     */
    public function postCheckout(
        $token,
        CheckoutRequest $request,
        PayPalPaymentService $payPalService,
        StripePaymentService $stripePaymentService,
        CodPaymentService $codPaymentService,
        BankTransferPaymentService $bankTransferPaymentService,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyCouponService $applyCouponService,
        HandleRemoveCouponService $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        if (!Cart::instance('cart')->count()) {
            return $response
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->user()->getAuthIdentifier();
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $shippingAmount = 0;

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute($token);
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_ship') ? 0 : $shippingAmount;
            }
        }

        $weight = 0;
        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = $this->productRepository->findById($cartItem->id);
            if ($product) {
                if ($product->weight) {
                    $weight += $product->weight;
                }
            }
        }

        $weight = $weight < 0.1 ? 0.1 : $weight;

        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');

        if ($request->has('shipping_method') && !get_shipping_setting('free_ship',
                $request->input('shipping_method'))) {

            $shippingData = [
                'address'     => Arr::get($sessionData, 'address'),
                'country'     => Arr::get($sessionData, 'country'),
                'state'       => Arr::get($sessionData, 'state'),
                'city'        => Arr::get($sessionData, 'city'),
                'weight'      => $weight,
                'order_total' => Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount,
            ];

            $shippingMethod = $shippingFeeService->execute($shippingData, $request->input('shipping_method'), null,
                $request->input('shipping_option'));

            $shippingAmount = Arr::get($shippingMethod, 'price', 0);
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute($token);
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_ship') ? 0 : $shippingAmount;
            }
        }

        $request->merge([
            'amount'          => Cart::instance('cart')
                    ->rawTotal() + $shippingAmount - $promotionDiscountAmount - $couponDiscountAmount,
            'currency'        => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id'         => $currentUserId,
            'shipping_method' => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
            'shipping_option' => $request->input('shipping_option'),
            'shipping_amount' => $shippingAmount,
            'tax_amount'      => Cart::instance('cart')->rawTax(),
            'sub_total'       => Cart::instance('cart')->rawSubTotal(),
            'coupon_code'     => session()->get('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status'          => OrderStatusEnum::PENDING,
            'is_finished'     => true,
        ]);

        $order = $this->orderRepository->getFirstBy(['token' => $token]);
        if ($order) {
            $order->fill($request->input());
            $order = $this->orderRepository->createOrUpdate($order);
        } else {
            $order = $this->orderRepository->createOrUpdate($request->input());
        }

        if ($order) {

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'create_order_from_payment_page',
                'description' => trans('Order is created from checkout page'),
                'order_id'    => $order->id,
            ]);

            $discount = $this->discountRepository
                ->getModel()
                ->where('code', session()->get('applied_coupon_code'))
                ->where('type', 'coupon')
                ->where('start_date', '<=', Carbon::now(config('app.timezone')))
                ->where(function ($query) {
                    /**
                     * @var Builder $query
                     */
                    return $query
                        ->whereNull('end_date')
                        ->orWhere('end_date', '>', Carbon::now(config('app.timezone')));
                })
                ->first();

            if (!empty($discount)) {
                $discount->total_used++;
                $this->discountRepository->createOrUpdate($discount);
            }

            $this->orderProductRepository->deleteBy(['order_id' => $order->id]);

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $data = [
                    'order_id'     => $order->id,
                    'product_id'   => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'qty'          => $cartItem->qty,
                    'weight'       => $weight,
                    'price'        => $cartItem->price,
                    'tax_amount'   => $cartItem->taxRate / 100 * $cartItem->price,
                    'options'      => [],
                ];
                if ($cartItem->options->product_version) {
                    $data['options']['product_version_id'] = $cartItem->options->product_version;
                    $data['options']['version_info'] = $cartItem->options->version_info;
                }

                $this->orderProductRepository->create($data);

                $this->productRepository
                    ->getModel()
                    ->where('id', $cartItem->id)
                    ->where('with_storehouse_management', 1)
                    ->where('quantity', '>', 0)
                    ->decrement('quantity', $cartItem->qty);
            }

            $request->merge([
                'order_id' => $order->id,
            ]);

            $paymentData = [
                'error'     => false,
                'message'   => false,
                'amount'    => $order->amount,
                'currency'  => strtoupper(get_application_currency()->title),
                'type'      => $request->input('payment_method'),
                'charge_id' => null,
            ];

            switch ($request->input('payment_method')) {
                case PaymentMethodEnum::STRIPE:
                    $result = $stripePaymentService->execute($request);
                    if (!$result) {
                        $paymentData['error'] = true;
                        $paymentData['message'] = $stripePaymentService->getErrorMessage();
                    } else {
                        $paymentData['charge_id'] = $result;
                    }

                    break;

                case PaymentMethodEnum::PAYPAL:
                    $checkoutUrl = $payPalService->execute($request);
                    if ($checkoutUrl) {
                        return redirect($checkoutUrl);
                    }

                    $paymentData['error'] = true;
                    $paymentData['message'] = $payPalService->getErrorMessage();
                    break;
                case PaymentMethodEnum::COD:
                    $paymentData['charge_id'] = $codPaymentService->execute($request);
                    break;

                case PaymentMethodEnum::BANK_TRANSFER:
                    $paymentData['charge_id'] = $bankTransferPaymentService->execute($request);
                    break;
                default:
                    $paymentData = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $paymentData, $request);
                    break;
            }

            OrderHelper::processOrder($order->id, $paymentData['charge_id']);

            if ($paymentData['error']) {
                return $response
                    ->setError()
                    ->setNextUrl(route('public.checkout.success', $token))
                    ->setMessage($paymentData['message']);
            }

            OrderHelper::clearSessions($token);

            return $response
                ->setNextUrl(route('public.checkout.success', $token))
                ->setMessage(__('Checkout successfully!'));
        }

        return $response
            ->setError()
            ->setMessage(__('There is an issue when ordering. Please try again later!'));
    }

    /**
     * @param string $token
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|Application|Factory|RedirectResponse|View
     */
    public function getCheckoutSuccess($token, BaseHttpResponse $response)
    {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $order = $this->orderRepository->getFirstBy(compact('token'), [], ['address', 'products']);

        if ($token !== session('tracked_start_checkout')) {
            if (!$order) {
                return $response->setNextUrl(url('/'));
            }
        }

        if (!$order) {
            return redirect()->to('/');
        }

        return view('plugins/ecommerce::orders.thank-you', compact('order'));
    }

    /**
     * @param ApplyCouponRequest $request
     * @param HandleApplyCouponService $handleApplyCouponService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postApplyCoupon(
        ApplyCouponRequest $request,
        HandleApplyCouponService $handleApplyCouponService,
        BaseHttpResponse $response
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $result = $handleApplyCouponService->execute($request);

        if ($result['error']) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    /**
     * @param Request $request
     * @param HandleRemoveCouponService $handleRemoveCouponService
     * @param BaseHttpResponse $response
     * @return array|BaseHttpResponse
     * @throws Throwable
     */
    public function postRemoveCoupon(
        Request $request,
        HandleRemoveCouponService $handleRemoveCouponService,
        BaseHttpResponse $response
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $result = $handleRemoveCouponService->execute($request->input('token'));

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }
            return $response
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    /**
     * @param PayPalPaymentCallbackRequest $request
     * @param PayPalPaymentService $palPaymentService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getPayPalStatus(
        PayPalPaymentCallbackRequest $request,
        PayPalPaymentService $palPaymentService,
        BaseHttpResponse $response
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $chargeId = $palPaymentService->afterMakePayment($request);

        if ($request->input('order_id')) {
            OrderHelper::processOrder($request->input('order_id'), $chargeId);
        }

        return $response
            ->setNextUrl(route('public.checkout.success', session('tracked_start_checkout')))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getTaxAmount(Request $request, BaseHttpResponse $response)
    {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $taxRules = $this->taxRepository->advancedGet(['order_by' => 'priority ASC']);
        $fieldName = $request->input('name');
        $fieldValue = $request->input('value');

        $findRule = null;
        foreach ($taxRules as $taxRule) {
            if (strtolower($taxRule->$fieldName) == strtolower($fieldValue) || strtolower($taxRule->$fieldName) == '*') {
                $findRule = $taxRule;
                break;
            }
        }

        if (empty($findRule)) {
            return $response->setError();
        }

        $taxAmount = 0;

        foreach (Cart::instance('cart')->content() as $item) {
            $taxAmount += ($item->price * $findRule->percentage) / 100;
        }

        return $response->setData([
            'tax_amount'      => $taxAmount,
            'tax_amount_text' => format_price($taxAmount),
        ]);
    }

    /**
     * @param string $token
     * @param Request $request
     * @param HandleShippingFeeService $shippingFeeService
     * @param HandleApplyPromotionsService $applyPromotionsService
     * @return Application|Factory|View
     */
    public function getCheckoutRecover(
        $token,
        Request $request,
        HandleShippingFeeService $shippingFeeService,
        HandleApplyPromotionsService $applyPromotionsService
    ) {
        if (get_ecommerce_setting('shopping_cart_enabled', '1') != '1') {
            abort(404);
        }

        $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => 0]);
        if (!$order) {
            abort(404);
        }

        $sessionCheckoutData = [
            'promotion_discount_amount' => $order->discount_amount,
            'name'                      => $order->address->name,
            'email'                     => $order->address->email,
            'phone'                     => $order->address->phone,
            'address'                   => $order->address->address,
            'country'                   => $order->address->country,
            'state'                     => $order->address->state,
            'city'                      => $order->address->city,
            'shipping_method'           => $order->shipping_method,
            'shipping_option'           => $order->shipping_option,
            'shipping_amount'           => $order->shipping_amount,
        ];

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $product = $this->productRepository->findById($orderProduct->product_id);
            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        $weight = 0;
        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = $this->productRepository->findById($cartItem->id);
            if ($product && $product->weight) {
                $weight += $product->weight;
            }
        }

        $weight = $weight >= 0.1 ? $weight : 0.1;

        $promotionDiscountAmount = $applyPromotionsService->execute($token);
        $couponDiscountAmount = 0;

        $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount;
        $orderTotal = $orderTotal > 0 ? $orderTotal : 0;

        $shippingData = [
            'address'     => Arr::get($sessionCheckoutData, 'address'),
            'country'     => Arr::get($sessionCheckoutData, 'country'),
            'state'       => Arr::get($sessionCheckoutData, 'state'),
            'city'        => Arr::get($sessionCheckoutData, 'city'),
            'weight'      => $weight,
            'order_total' => $orderTotal,
        ];

        $shipping = $shippingFeeService->execute($shippingData);

        foreach ($shipping as $key => &$shippingItem) {
            if (get_shipping_setting('free_ship', $key)) {
                foreach ($shippingItem as &$subShippingItem) {
                    Arr::set($subShippingItem, 'price', 0);
                }
            }
        }

        $defaultShippingMethod = $request->input('shipping_method',
            old('shipping_method', Arr::get($sessionCheckoutData, 'shipping_method', Arr::first(array_keys($shipping)))));

        $defaultShippingOption = null;
        if (!empty($shipping)) {
            $defaultShippingOption = Arr::first(array_keys(Arr::first($shipping)));
            $defaultShippingOption = $request->input('shipping_option',
                old('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option') ?? $defaultShippingOption));
        }
        $shippingAmount = Arr::get($shipping, $defaultShippingMethod . '.' . $defaultShippingOption . '.price', 0);

        Arr::set($sessionCheckoutData, 'shipping_method', $defaultShippingMethod);
        Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
        Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);
        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return view('plugins/ecommerce::orders.checkout', compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData'
        ));
    }
}
