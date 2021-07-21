@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200">
        <div class="ui-layout">
            <div class="flexbox-layout-sections" id="main-order-content">
                @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                    <div class="ui-layout__section">
                        <div class="ui-layout__item">
                            <div class="ui-banner ui-banner--status-warning">
                                <div class="ui-banner__ribbon">
                                    <svg class="svg-next-icon svg-next-icon-size-20">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#alert-circle"></use>
                                    </svg>
                                </div>
                                <div class="ui-banner__content">
                                    <h2 class="ui-banner__title">{{ __('Cancel order') }}</h2>
                                    <div class="ws-nm">
                                        {{ __('Order was canceled at') }} <strong>{{ BaseHelper::formatDate($order->updated_at, 'H:i d/m/Y') }}</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="flexbox-layout-section-primary mt20">
                    <div class="ui-layout__item">
                        <div class="wrapper-content">
                            <div class="pd-all-20">
                                <div class="flexbox-grid-default">
                                    <div class="flexbox-auto-right mr5">
                                        <label class="title-product-main text-no-bold">{{ __('Order information') }} {{ get_order_code($order->id) }}</label>
                                    </div>
                                </div>
                                <div class="mt20">
                                    @if ($order->shipment->id)
                                        <svg class="svg-next-icon svg-next-icon-size-16 next-icon--right-spacing-quartered">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-orders"></use>
                                        </svg>
                                        <strong class="ml5">{{ __('Completed') }}</strong>
                                    @else
                                        <svg class="svg-next-icon svg-next-icon-size-16 svg-next-icon-gray">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-order-unfulfilled-16"></use>
                                        </svg>
                                        <strong class="ml5">{{ __('Uncompleted') }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="pd-all-20 p-none-t border-top-title-main">
                                <div class="table-wrap">
                                    <table class="table-order table-divided">
                                        <tbody>
                                            @foreach ($order->products as $orderProduct)
                                                @php
                                                    $product = get_products([
                                                        'condition' => [
                                                            'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                                                            'ec_products.id' => $orderProduct->product_id,
                                                        ],
                                                        'take' => 1,
                                                        'select' => [
                                                            'ec_products.id',
                                                            'ec_products.images',
                                                            'ec_products.name',
                                                            'ec_products.price',
                                                            'ec_products.sale_price',
                                                            'ec_products.sale_type',
                                                            'ec_products.start_date',
                                                            'ec_products.end_date',
                                                            'ec_products.sku',
                                                            'ec_products.is_variation',
                                                        ],
                                                    ]);
                                                @endphp
                                                @if ($product)
                                                    <tr>
                                                    <td class="width-60-px min-width-60-px vertical-align-t">
                                                        <div class="wrap-img"><img class="thumb-image thumb-image-cartorderlist" src="{{ RvMedia::getImageUrl($product->original_product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"></div>
                                                    </td>
                                                    <td class="pl5 p-r5 min-width-200-px">
                                                        <a class="text-underline hover-underline pre-line" target="_blank" href="{{ route('products.edit', $product->original_product->id) }}" title="{{ $orderProduct->product_name }}">
                                                            {{ $orderProduct->product_name }}
                                                        </a>
                                                        <p>
                                                            @php $attributes = get_product_attributes($product->id) @endphp
                                                            @if (!empty($attributes))
                                                                @foreach ($attributes as $attr)
                                                                    @if (!$loop->last)
                                                                        {{ $attr->attribute_set_title }}: {{ $attr->title }} <br>
                                                                    @else
                                                                        {{ $attr->attribute_set_title }}: {{ $attr->title }}
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </p>
                                                        @if ($product->sku)
                                                            <p>{{ __('SKU') }} : <span>{{ $product->sku }}</span></p>
                                                        @endif
                                                        @if ($order->shipment->id)
                                                            <ul class="unstyled">
                                                                <li class="simple-note">
                                                                    <a><span>{{ $orderProduct->qty }}</span><span> {{ __('completed') }}</span></a>
                                                                    <ul class="dom-switch-target line-item-properties small">
                                                                        <li class="ws-nm">
                                                                            <span class="bull">↳</span>
                                                                            <span class="black">{{ __('Shipping') }} </span>
                                                                            <a class="text-underline bold-light" target="_blank" title="{{ $order->shipping_method_name }}" href="{{ route('ecommerce.shipments.edit', $order->shipment->id) }}">{{ $order->shipping_method_name }}</a>
                                                                        </li>
                                                                        <li class="ws-nm">
                                                                            <span class="bull">↳</span>
                                                                            <span class="black">{{ __('Warehouse') }}</span>
                                                                            <span class="bold-light">{{ $order->shipment->store->name ?? $defaultStore->name }}</span>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        @endif
                                                    </td>
                                                    <td class="pl5 p-r5 text-right">
                                                        <div class="inline_block">
                                                            <span>{{ format_price($orderProduct->price) }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="pl5 p-r5 text-center">x</td>
                                                    <td class="pl5 p-r5">
                                                        <span>{{ $orderProduct->qty }}</span>
                                                    </td>
                                                    <td class="pl5 text-right">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="pd-all-20 p-none-t">
                                <div class="flexbox-grid-default block-rps-768">
                                    <div class="flexbox-auto-right p-r5">

                                    </div>
                                    <div class="flexbox-auto-right pl5">
                                        <div class="table-wrap">
                                            <table class="table-normal table-none-border table-color-gray-text">
                                                <tbody>
                                                <tr>
                                                    <td class="text-right color-subtext">{{ __('Total amount') }}</td>
                                                    <td class="text-right pl10">
                                                        <span>{{ format_price($order->amount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext mt10">
                                                        <p class="mb0">{{ __('Discount') }}</p>
                                                        @if ($order->coupon_code)
                                                            <p class="mb0">{!! __('Coupon code: ":code"', ['code' => Html::tag('strong', $order->coupon_code)->toHtml()])  !!}</p>
                                                        @elseif ($order->discount_description)
                                                            <p class="mb0">{{ $order->discount_description }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="text-right p-none-b pl10">
                                                        <p class="mb0">{{ format_price($order->discount_amount) }}</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext mt10">
                                                        <p class="mb0">{{ __('Shipping fee') }}</p>
                                                        <p class="mb0 font-size-12px">{{ $order->shipping_method_name }}</p>
                                                        <p class="mb0 font-size-12px">{{ ecommerce_convert_weight($weight) }} {{ ecommerce_weight_unit(true) }}</p>
                                                    </td>
                                                    <td class="text-right p-none-t pl10">
                                                        <p class="mb0">{{ format_price($order->shipping_amount) }}</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext mt10">
                                                        <p class="mb0">{{ __('Tax') }}</p>
                                                    </td>
                                                    <td class="text-right p-none-t pl10">
                                                        <p class="mb0">{{ format_price($order->tax_amount, $order->currency_id) }}</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right mt10">
                                                        <p class="mb0 text-no-bold">{{ __('Total amount') }}</p>
                                                        @if ($order->payment->id)
                                                            <p class="mb0  font-size-12px"><a href="{{ route('payment.show', $order->payment->id) }}" target="_blank">{{ $order->payment->payment_channel->label() }}</a></p>
                                                        @endif
                                                    </td>
                                                    <td class="text-right text-no-bold p-none-t pl10">
                                                        @if ($order->payment->id)
                                                            <a href="{{ route('payment.show', $order->payment->id) }}" target="_blank">
                                                                <span>{{ format_price($order->amount) }}</span>
                                                            </a>
                                                        @else
                                                            <span>{{ format_price($order->amount) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="border-bottom"></td>
                                                    <td class="border-bottom"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right color-subtext">{{ __('Paid amount') }}</td>
                                                    <td class="text-right color-subtext pl10">
                                                        @if ($order->payment->id)
                                                            <a href="{{ route('payment.show', $order->payment->id) }}" target="_blank">
                                                                <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->payment->amount : 0) }}</span>
                                                            </a>
                                                        @else
                                                            <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->payment->amount : 0) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::REFUNDED)
                                                    <tr class="hidden">
                                                        <td class="text-right color-subtext">{{ __('Refunded amount') }}</td>
                                                        <td class="text-right pl10">
                                                            <span>{{ format_price($order->payment->amount) }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr class="hidden">
                                                    <td class="text-right color-subtext">{{ __('The amount actually received') }}</td>
                                                    <td class="text-right pl10">
                                                        <span>{{ format_price($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED ? $order->amount : 0) }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <div class="text-right">
                                            <a href="{{ route('orders.generate-invoice', $order->id) }}" class="btn btn-info">
                                                <i class="fa fa-download"></i> {{ __('Download invoice') }}
                                            </a>
                                        </div>
                                        <div class="pd-all-20">
                                            <form action="{{ route('orders.edit', $order->id) }}">
                                                <label class="text-title-field">{{ __('Note') }}</label>
                                                <textarea class="ui-text-area textarea-auto-height" name="description" rows="3" placeholder="{{ __('Add note…') }}">{{ $order->description }}</textarea>
                                                <div class="mt10">
                                                    <button type="button" class="btn btn-primary btn-update-order">{{ __('Save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pd-all-20 border-top-title-main">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-left">
                                        <svg class="svg-next-icon svg-next-icon-size-20 @if ($order->is_confirmed) svg-next-icon-green @else svg-next-icon-gray @endif">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                        </svg>
                                    </div>
                                    <div class="flexbox-auto-right ml15 mr15 text-upper">
                                        @if ($order->is_confirmed)
                                            <span>{{ __('Order was confirmed') }}</span>
                                        @else
                                            <span>{{ __('Confirm order') }}</span>
                                        @endif
                                    </div>
                                    @if (!$order->is_confirmed)
                                        <div class="flexbox-auto-left">
                                            <form action="{{ route('orders.confirm') }}">
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <button class="btn btn-primary btn-confirm-order">{{ __('Confirm') }}</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="pd-all-20 border-top-title-main">
                                <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                                    @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED)
                                        <div class="flexbox-auto-left">
                                            <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-error"></use>
                                            </svg>
                                        </div>
                                        <div class="flexbox-auto-content ml15 mr15 text-upper">
                                            <span>{{ __('Order was cancelled') }}</span>
                                        </div>
                                    @elseif ($order->payment->id)
                                        <div class="flexbox-auto-left">
                                            @if (!$order->payment->status || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-credit-card"></use>
                                                </svg>
                                            @elseif ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flexbox-auto-content ml15 mr15 text-upper">
                                            @if (!$order->payment->status || $order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                                                <span>{{ __('Pending payment') }}</span>
                                            @elseif ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED)
                                                <span>{{ __('Payment :money was accepted', ['money' => format_price($order->payment->amount - $order->payment->refunded_amount)]) }}</span>
                                            @elseif ($order->payment->amount - $order->payment->refunded_amount == 0)
                                                <span>{{ __('Payment was refunded') }}</span>
                                            @endif
                                        </div>
                                        @if (!$order->payment->status || in_array($order->payment->status, [\Botble\Payment\Enums\PaymentStatusEnum::PENDING]))
                                            <div class="flexbox-auto-left">
                                                <button class="btn btn-primary btn-trigger-confirm-payment" data-target="{{ route('orders.confirm-payment', $order->id) }}">{{ __('Confirm payment') }}</button>
                                            </div>
                                        @endif
                                        @if ($order->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::COMPLETED && (($order->payment->amount - $order->payment->refunded_amount > 0) || ($order->products->sum('qty') - $order->products->sum('restock_quantity') > 0)))
                                            <div class="flexbox-auto-left">
                                                <button class="btn btn-secondary ml10 btn-trigger-refund">{{ __('Refund') }}</button>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="pd-all-20 border-top-title-main">
                                <div class="flexbox-grid-default flexbox-flex-wrap flexbox-align-items-center">
                                    @if ($order->status == \Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED && !$order->shipment->id)
                                        <div class="flexbox-auto-left">
                                            <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                            </svg>
                                        </div>
                                        <div class="flexbox-auto-content ml15 mr15 text-upper">
                                            <span>{{ __('All products are not delivered') }}</span>
                                        </div>
                                    @else
                                        @if ($order->shipment->id)
                                            <div class="flexbox-auto-left">
                                                <svg class="svg-next-icon svg-next-icon-size-20 svg-next-icon-green">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-checkmark"></use>
                                                </svg>
                                            </div>
                                            <div class="flexbox-auto-content ml15 mr15 text-upper">
                                                <span>{{ __('Delivery') }}</span>
                                            </div>
                                        @else
                                            <div class="flexbox-auto-left">
                                                <svg class="svg-next-icon svg-next-icon-size-24 svg-next-icon-gray">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-shipping"></use>
                                                </svg>
                                            </div>
                                            <div class="flexbox-auto-content ml15 mr15 text-upper">
                                                <span>{{ __('Delivery') }}</span>
                                            </div>
                                            <div class="flexbox-auto-left">
                                                <div class="item">
                                                    <button class="btn btn-primary btn-trigger-shipment" data-target="{{ route('orders.get-shipment-form', $order->id) }}">{{ __('Delivery') }}</button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            @if (!$order->shipment->id)
                                <div class="shipment-create-wrap hidden"></div>
                            @else
                                @include('plugins/ecommerce::orders.shipment-detail', ['shipment' => $order->shipment])
                            @endif
                        </div>
                        <div class="mt20 mb20">
                            <div>
                                <div class="comment-log ws-nm">
                                    <div class="comment-log-title">
                                        <label class="bold-light m-xs-b hide-print">{{ __('History') }}</label>
                                    </div>
                                    <div class="comment-log-timeline">
                                        <div class="column-left-history ps-relative" id="order-history-wrapper">
                                            @foreach ($order->histories()->orderBy('id', 'DESC')->get() as $history)
                                                <div class="item-card">
                                                    <div class="item-card-body clearfix">
                                                        <div class="item comment-log-item comment-log-item-date ui-feed__timeline">
                                                            <div class="ui-feed__item ui-feed__item--message">
                                                                <span class="ui-feed__marker @if ($history->user_id) ui-feed__marker--user-action @endif"></span>
                                                                <div class="ui-feed__message">
                                                                    <div class="timeline__message-container">
                                                                        <div class="timeline__inner-message">
                                                                            @if (in_array($history->action, ['confirm_payment', 'refund']))
                                                                                <a href="#" class="text-no-bold show-timeline-dropdown hover-underline" data-target="#history-line-{{ $history->id }}">
                                                                                    <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                                </a>
                                                                            @else
                                                                                <span>{{ OrderHelper::processHistoryVariables($history) }}</span>
                                                                            @endif
                                                                        </div>
                                                                        <time class="timeline__time"><span>{{ $history->created_at }}</span></time>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($history->action == 'refund' && Arr::get($history->extras, 'amount', 0) > 0)
                                                                <div class="timeline-dropdown" id="history-line-{{ $history->id }}">
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>{{ __('Order number') }}</th>
                                                                            <td><a href="{{ route('orders.edit', $order->id) }}" title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Description') }}</th>
                                                                            <td>{{ $history->description . ' từ ' . $order->payment->payment_channel->label() }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Amount') }}</th>
                                                                            <td>{{ format_price(Arr::get($history->extras, 'amount', 0)) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Status') }}</th>
                                                                            <td>{{ __('Successfully') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Transaction\'s type') }}</th>
                                                                            <td>{{ __('Refund') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Staff') }}</th>
                                                                            <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : __('N/A') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Refund date') }}</th>
                                                                            <td>{{ $history->created_at }}</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                            @if ($history->action == 'confirm_payment' && $order->payment)
                                                                <div class="timeline-dropdown" id="history-line-{{ $history->id }}">
                                                                    <table>
                                                                        <tbody>
                                                                        <tr>
                                                                            <th>{{ __('Order number') }}</th>
                                                                            <td><a href="{{ route('orders.edit', $order->id) }}" title="{{ get_order_code($order->id) }}">{{ get_order_code($order->id) }}</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Description') }}</th>
                                                                            <td>{!! __('Mark <span>:method</span> as confirmed', ['method' => $order->payment->payment_channel->label()]) !!}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Transaction amount') }}</th>
                                                                            <td>{{ format_price($order->payment->amount) }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Payment gateway') }}</th>
                                                                            <td>{{ $order->payment->payment_channel->label() }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Status') }}</th>
                                                                            <td>{{ __('Successfully') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Transaction type') }}</th>
                                                                            <td>{{ __('Confirm') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Staff') }}</th>
                                                                            <td>{{ $order->payment->user->getFullName() ? $order->payment->user->getFullName() : __('N/A') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('Payment date') }}</th>
                                                                            <td>{{ $history->created_at }}</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                            @if ($history->action == 'send_order_confirmation_email')
                                                                <div class="ui-feed__item ui-feed__item--action">
                                                                    <span class="ui-feed__spacer"></span>
                                                                    <div class="timeline__action-group">
                                                                        <a href="#" class="btn hide-print timeline__action-button hover-underline btn-trigger-resend-order-confirmation-modal" data-action="{{ route('orders.send-order-confirmation-email', $history->order_id) }}">{{ __('Resend') }}</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="flexbox-layout-section-secondary mt20">
                    <div class="ui-layout__item">
                        <div class="wrapper-content mb20">
                            <div class="next-card-section p-none-b">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-content-left">
                                        <label class="title-product-main text-no-bold">{{ __('Customer') }}</label>
                                    </div>
                                    <div class="flexbox-auto-left">
                                        <img class="width-30-px radius-cycle" width="40" src="{{ $order->user->id ? $order->user->avatar_url : $order->address->avatar_url }}">
                                    </div>
                                </div>
                            </div>
                            <div class="next-card-section border-none-t">
                                <div class="mb5">
                                    <a class="hover-underline text-capitalize" href="#">{{ $order->user->name ? $order->user->name : $order->address->name }}</a>
                                </div>
                                @if ($order->user->id)
                                    <div><i class="fas fa-inbox mr5"></i><span>{{ $order->user->orders()->count() }}</span> {{ __('order(s)') }}</div>
                                @endif
                                <ul class="ws-nm text-infor-subdued">
                                    <li class="overflow-ellipsis"><a class="hover-underline" href="mailto:{{ $order->user->email ? $order->user->email : $order->address->email }}">{{ $order->user->email ? $order->user->email : $order->address->email }}</a></li>
                                    @if ($order->user->id)
                                        <li><div>{{ __('Have an account already') }}</div></li>
                                    @else
                                        <li><div>{{ __('Don\'t have an account yet') }}</div></li>
                                    @endif
                                </ul>
                            </div>
                            <div class="next-card-section">
                                <div class="flexbox-grid-default flexbox-align-items-center">
                                    <div class="flexbox-auto-content-left">
                                        <label class="title-text-second">{{ __('Shipping address') }}</label>
                                    </div>
                                    <div class="flexbox-auto-content-right text-right">
                                        <a href="#" class="btn-trigger-update-shipping-address">
                                        <span data-placement="top" title="" data-toggle="tooltip" data-original-title="{{ __('Update address') }}">
                                            <svg class="svg-next-icon svg-next-icon-size-12">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-edit"></use>
                                            </svg>
                                        </span>
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <ul class="ws-nm text-infor-subdued shipping-address-info">
                                        @include('plugins/ecommerce::orders.shipping-address.detail', ['address' => $order->address])
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">
                                <div class="p-b10">
                                    <span>{{ __('Warehouse') }}</span>
                                    <ul class="pl25 p-sm-r">
                                        <li class="ws-nm">
                                            <span class="ww-bw text-no-bold">{{ $defaultStore->name ?? __('Default store') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="wrapper-content bg-gray-white mb20">
                            <div class="pd-all-20">
                                <a href="{{ route('orders.reorder', ['order_id' => $order->id]) }}" class="btn btn-info">{{ __('Reorder') }}</a>&nbsp;
                                @if (!in_array($order->status, [\Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED, \Botble\Ecommerce\Enums\OrderStatusEnum::COMPLETED]))
                                    <a href="#" class="btn btn-secondary btn-trigger-cancel-order" data-target="{{ route('orders.cancel', $order->id) }}">{{ __('Cancel') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::modalAction('resend-order-confirmation-email-modal', __('Resend order confirmation'), 'info', __('Confirmation email will be sent to <strong>:email</strong>?', ['email' => $order->user->id ? $order->user->email : $order->address->email]), 'confirm-resend-confirmation-email-button', __('Send')) !!}
    {!! Form::modalAction('cancel-shipment-modal', __('Cancel order confirmation?'), 'info', __('Are you sure you want to cancel this order?'), 'confirm-cancel-shipment-button', __('Confirm')) !!}
    {!! Form::modalAction('update-shipping-address-modal', __('Update address'), 'info', view('plugins/ecommerce::orders.shipping-address.form', ['address' => $order->address, 'orderId' => $order->id])->render(), 'confirm-update-shipping-address-button', __('Update'), 'modal-md') !!}
    {!! Form::modalAction('cancel-order-modal', __('Confirm cancel order?'), 'info', __('Are you sure you want to cancel this order? This action cannot rollback.'), 'confirm-cancel-order-button', __('Cancel order confirmation')) !!}
    {!! Form::modalAction('confirm-payment-modal', __('Confirm payment?'), 'info', __('Processed by <strong>:method</strong>. Did you receive payment outside the system? This payment won\'t be saved into system and cannot be refunded', ['method' => $order->payment->payment_channel->label()]), 'confirm-payment-order-button', __('Confirm payment')) !!}
    {!! Form::modalAction('confirm-refund-modal', __('Refund'), 'info', view('plugins/ecommerce::orders.refund.modal', compact('order'))->render(), 'confirm-refund-payment-button', __('Refund') . ' <span class="refund-amount-text">' . format_price($order->payment->amount - $order->payment->refunded_amount, true) . '</span>') !!}
@stop
