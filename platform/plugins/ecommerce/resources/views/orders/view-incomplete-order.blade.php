@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1036">
        <div class="ui-layout__item mb20">
            <div class="ui-banner ui-banner--status-info">
                <div class="ui-banner__ribbon">
                    <svg class="svg-next-icon svg-next-icon-size-20">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-cart"></use>
                    </svg>
                </div>
                <div class="ui-banner__content ws-nm">
                    <h2 class="ui-banner__title">
                        {{ __('An incomplete order is when a potential customer places items in their shopping cart, and goes all the way through to the payment page, but then doesn\'t complete the transaction.') }}
                    </h2>
                    <h2 class="ui-banner__title">
                        {{ __('If you have contacted customers and they want to continue buying, you can help them complete their order by following the link:') }}
                    </h2>
                    <div class="ws-nm">
                        <input type="text" class="next-input" onclick="this.focus(); this.select();" value="{{ route('public.checkout.recover', $order->token) }}">
                        <br>
                        <button class="btn btn-secondary btn-trigger-send-order-recover-modal" data-action="{{ route('orders.send-order-recover-email', $order->id) }}">{{ __('Send an email to customer to recover this order') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flexbox-grid">
            <div class="flexbox-content">
                <div class="wrapper-content mb20">
                    <div class="pd-all-20">
                        <label class="title-product-main text-no-bold">{{ __('Order information') }}</label>
                    </div>
                    <div class="pd-all-10-20 border-top-title-main">
                        <div class="clearfix">
                            <div class="table-wrapper p-none mb20 ps-relative">
                                <table class="table-normal">
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
                                                    <td class="width-60-px min-width-60-px">
                                                        <div class="wrap-img"><img class="thumb-image thumb-image-cartorderlist" src="{{ RvMedia::getImageUrl($product->original_product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"></div>
                                                    </td>
                                                    <td class="pl5 p-r5">
                                                        <a target="_blank" href="{{ route('products.edit', $product->original_product->id) }}" title="{{ $orderProduct->product_name }}">{{ $orderProduct->product_name }}</a>
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
                                                    </td>
                                                    <td class="pl5 p-r5 width-100-px min-width-100-px text-right">
                                                        <span>{{ format_price($orderProduct->price) }}</span>
                                                    </td>
                                                    <td class="pl5 p-r5 width-20-px min-width-20-px text-center"> x</td>
                                                    <td class="pl5 p-r5 width-30-px min-width-30-px text-left">
                                                        <span class="item-quantity text-right">{{ $orderProduct->qty }}</span>
                                                    </td>
                                                    <td class="pl5 p-r5 width-100-px min-width-130-px text-right">{{ format_price($orderProduct->price * $orderProduct->qty) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="flexbox-grid-default">
                            <div class="flexbox-auto-content"></div>
                            <div class="flexbox-auto-content">
                                <div class="table-wrapper">
                                    <table class="table-normal table-none-border">
                                        <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right p-sm-r">
                                                {{ __('Order amount') }}:
                                            </td>
                                            <td class="text-right p-r5">{{ format_price($order->amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right p-sm-r">
                                                {{ __('Total amount') }}:
                                            </td>
                                            <td class="text-right p-r5">{{ format_price($order->amount + $order->shipping_amount - $order->discount_amount) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wrapper-content mb20">
                    <div class="pd-all-20 p-none-b">
                        <label class="title-product-main text-no-bold">{{ __('Additional information') }}</label>
                    </div>
                    <div class="pd-all-10-20">
                        <form action="{{ route('orders.edit', $order->id) }}">
                            <label class="text-title-field">{{ __('Order note') }}</label>
                            <textarea class="ui-text-area textarea-auto-height" name="description" placeholder="Note about order, ex: time or shipping instruction." rows="2">{{ $order->description }}</textarea>
                            <div class="mt15 mb15 text-right">
                                <button type="button" class="btn btn-primary btn-update-order">{{ __('Save note') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flexbox-content flexbox-right">
                <div class="wrapper-content mb20">
                    <div class="next-card-section p-none-b">
                        <div class="flexbox-grid-default">
                            <div class="flexbox-auto-content">
                                <label class="title-product-main"><strong>{{ __('Customer') }}</strong></label>
                            </div>
                            <div class="flexbox-auto-left">
                                <img class="width-30-px radius-cycle" width="40" src="{{ $order->user->id ? $order->user->avatar_url : $order->address->avatar_url }}" alt="{{ $order->address->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="next-card-section border-none-t">
                        <ul class="ws-nm">
                            <li class="overflow-ellipsis">
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
                            </li>
                        </ul>
                    </div>
                    <div class="next-card-section">
                        <ul class="ws-nm">
                            <li class="clearfix">
                                <div class="flexbox-grid-default">
                                    <div class="flexbox-auto-content">
                                        <label class="title-text-second"><strong>{{ __('Shipping address') }}</strong></label>
                                    </div>
                                </div>
                            </li>
                            <li class="text-infor-subdued mt15">
                                <div>{{ $order->address->name }}</div>
                                <div>
                                    <a href="tel:{{ $order->address->phone }}">
                                        <span><i class="fa fa-phone-square cursor-pointer mr5"></i></span>
                                        <span>{{ $order->address->phone }}</span>
                                    </a>
                                </div>
                                <div>
                                    <div>{{ $order->address->address }}</div>
                                    <div>{{ $order->address->city }}</div>
                                    <div>{{ $order->address->state }}</div>
                                    <div>{{ $order->address->country_name }}</div>
                                    <div>
                                        <a target="_blank" class="hover-underline" href="https://maps.google.com/?q={{ $order->address->address }}, {{ $order->address->city }}, {{ $order->address->state }}, {{ $order->address->country_name }}">{{ __('See maps') }}</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::modalAction('send-order-recover-email-modal', __('Notice about uncompleted order'), 'info', __('Remind email about uncompleted order will be send to <strong>:email</strong>?', ['email' => $order->user->id ? $order->user->email : $order->address->email]), 'confirm-send-recover-email-button', __('Send')) !!}
@stop
