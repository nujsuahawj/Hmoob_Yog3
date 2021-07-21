<?php

namespace Botble\Ecommerce\Supports;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleInterface;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Cart;
use EmailHandler;
use Exception;
use File;
use Html;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Log;
use PDF;
use RvMedia;
use Throwable;

class OrderHelper
{
    /**
     * @param string $orderId
     * @param string $chargeId
     * @param bool $paymentDone
     * @return string
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function processOrder($orderId, $chargeId = null)
    {
        $order = app(OrderInterface::class)->findById($orderId);

        if (!$order) {
            return false;
        }

        if ($chargeId) {
            $payment = app(PaymentInterface::class)->getFirstBy(['charge_id' => $chargeId]);

            if ($payment) {
                $order->payment_id = $payment->id;
                $order->save();
            }
        }

        Cart::instance('cart')->destroy();
        session()->forget('applied_coupon_code');

        session(['order_id' => $orderId]);

        $invoice = $this->generateInvoice($order);

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('admin_new_order')) {
            $this->setEmailVariables($order);
            $mailer->sendUsingTemplate('admin_new_order', setting('admin_email'), ['attachments' => [$invoice]]);
        }

        session(['order_id' => $order->id]);

        app(OrderHistoryInterface::class)->createOrUpdate([
            'action'      => 'create_order',
            'description' => __('New order :order_id from :customer', [
                'order_id' => get_order_code($order->id),
                'customer' => $order->user->name ? $order->user->name : $order->address->name,
            ]),
            'order_id'    => $order->id,
        ]);

        $this->sendOrderConfirmationEmail($order, true);

        return $invoice;
    }

    /**
     * @param Order $order
     * @return string
     */
    public function generateInvoice($order)
    {
        $folderPath = storage_path('app/public');
        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath);
        }
        $invoice = $folderPath . '/invoice-order-' . get_order_code($order->id) . '.pdf';

        PDF::loadView('plugins/ecommerce::invoices.template', compact('order'))
            ->setPaper('a4')
            ->setWarnings(false)
            ->save($invoice);

        return $invoice;
    }

    /**
     * @param Order $order
     * @return \Botble\Base\Supports\EmailHandler
     * @throws Throwable
     */
    public function setEmailVariables($order)
    {
        return EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'store_address'    => get_ecommerce_setting('store_address'),
                'store_phone'      => get_ecommerce_setting('store_phone'),
                'order_id'         => str_replace('#', '', get_order_code($order->id)),
                'order_token'      => $order->token,
                'customer_name'    => $order->user->name ? $order->user->name : $order->address->name,
                'customer_email'   => $order->user->email ? $order->user->email : $order->address->email,
                'customer_phone'   => $order->user->phone ? $order->user->phone : $order->address->phone,
                'customer_address' => $order->address->address,
                'product_list'     => view('plugins/ecommerce::emails.partials.order-detail', compact('order'))
                    ->render(),
                'shipping_method'  => $this->getShippingMethod($order->shipping_method),
                'payment_method'   => $order->payment->payment_channel->label(),
            ]);
    }

    /**
     * @param string $method
     * @param null $option
     * @return array|null|string
     */
    public function getShippingMethod($method, $option = null)
    {
        $name = null;
        switch ($method) {
            default:
                if ($option) {
                    $rule = app(ShippingRuleInterface::class)->findById($option);
                    if ($rule) {
                        $name = $rule->name;
                    }
                }

                if (empty($name)) {
                    $name = __('Default');
                }
                break;
        }
        return $name;
    }

    /**
     * @param Order $order
     * @param bool $saveHistory
     * @return boolean
     * @throws Throwable
     */
    public function sendOrderConfirmationEmail($order, $saveHistory = false)
    {
        try {
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_new_order')) {
                $this->setEmailVariables($order);

                EmailHandler::send(
                    $mailer->getTemplateContent('customer_new_order'),
                    $mailer->getTemplateSubject('customer_new_order'),
                    $order->user->email ? $order->user->email : $order->address->email
                );

                if ($saveHistory) {
                    app(OrderHistoryInterface::class)->createOrUpdate([
                        'action'      => 'send_order_confirmation_email',
                        'description' => __('The email confirmation was sent to customer'),
                        'order_id'    => $order->id,
                    ]);
                }
            }
            return true;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return false;
    }

    /**
     * @param string $method
     * @return string
     */
    public function getShippingDetailUrl($method)
    {
        $link = null;
        switch ($method) {
            default:
                $link = '#';
                break;
        }
        return $link;
    }

    /**
     * @param OrderHistory $history
     * @return mixed
     */
    public function processHistoryVariables($history)
    {
        if (empty($history)) {
            return null;
        }

        $variables = [
            'order_id'  => Html::link(route('orders.edit', $history->order->id), get_order_code($history->order->id))
                ->toHtml(),
            'user_name' => $history->user_id === 0 ? __('System') :
                ($history->user ? $history->user->getFullName() : ($history->order->user->name ?
                    $history->order->user->name :
                    $history->order->address->name
                )),
        ];

        $content = $history->description;

        foreach ($variables as $key => $value) {
            $content = str_replace('% ' . $key . ' %', $value, $content);
            $content = str_replace('%' . $key . '%', $value, $content);
            $content = str_replace('% ' . $key . '%', $value, $content);
            $content = str_replace('%' . $key . ' %', $value, $content);
        }

        return $content;
    }

    /**
     * @param string $token
     * @param string|array $data
     * @return bool
     */
    public function setOrderSessionData($token, $data): bool
    {
        $data = array_merge($this->getOrderSessionData($token), $data);
        session([md5('checkout_address_information_' . $token) => $data]);

        return true;
    }

    /**
     * @param string $token
     * @return array|SessionManager|Store|mixed
     */
    public function getOrderSessionData($token)
    {
        $sessionData = [];
        $sessionKey = md5('checkout_address_information_' . $token);
        if (session()->has($sessionKey)) {
            $sessionData = session($sessionKey);
        }

        return $sessionData;
    }

    /**
     * @param string $token
     */
    public function clearSessions($token)
    {
        Cart::instance('cart')->destroy();
        session()->forget('applied_coupon_code');
        session()->forget('order_id');
        session()->forget(md5('checkout_address_information_' . $token));
        session()->forget('tracked_start_checkout');
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function handleAddCart($product, $request)
    {
        /**
         * With product attribute
         */
        $parentProduct = get_parent_product($product->id);
        $productAttributesString = '';
        if (get_product_attributes($product->id)->count() > 0) {
            $productAttributes = get_product_attributes($product->id);

            $productAttributesString .= '(';

            foreach ($productAttributes as $index => $attribute) {
                $productAttributesString .= $attribute->attribute_set_title . ': ' . $attribute->title;

                if ($index < count($productAttributes) - 1) {
                    $productAttributesString .= ' ';
                }
            }
            $productAttributesString .= ')';
        }

        /**
         * Add cart to session
         */
        Cart::instance('cart')->add(
            $product->id,
            $parentProduct->name,
            $request->input('qty', 1),
            $product->original_price,
            [
                'image'      => RvMedia::getImageUrl($product->image, 'thumb', false, $parentProduct->image),
                'attributes' => $productAttributesString,
                'taxRate'    => $parentProduct->tax->percentage,
            ]
        );

        /**
         * prepare data for response
         */
        $cartItems = [];
        foreach (Cart::instance('cart')->content() as $item) {
            array_push($cartItems, $item);
        }

        return $cartItems;
    }
}
