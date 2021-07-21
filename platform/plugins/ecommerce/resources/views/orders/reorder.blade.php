@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order
                :products="{{ json_encode($products->toArray()) }}"
                :product_ids="{{ json_encode($productIds) }}"
                @if ($customer)
                    :customer="{{ $customer }}"
                @endif
                :customer_id="{{ $order->user_id }}"
                :customer_addresses="{{ json_encode($customerAddresses) }}"
                :customer_address="{{ $customerAddress }}"
                :sub_amount="{{ $order->amount }}"
                :total_amount="{{ $order->payment->amount ?? $order->amount }}"
                :discount_amount="{{ $order->discount_amount }}"
                @if ($order->coupon_code) :discount_coupon_code="'{{ $order->coupon_code }}'" @endif
                @if ($order->discount_description) :discount_description="'{{ $order->discount_description }}'" @endif
                :shipping_amount="{{ $order->shipping_amount }}"
                @if ($order->shipping_method != \Botble\Ecommerce\Enums\ShippingMethodEnum::DEFAULT)
                    :shipping_method="'{{ $order->shipping_method }}'"
                @endif
                @if ($order->shipping_option) :shipping_option="'{{ $order->shipping_option }}'" @endif
                @if ($order->shipping_method != \Botble\Ecommerce\Enums\ShippingMethodEnum::DEFAULT && false)
                    :shipping_method_name="'{{ OrderHelper::getShippingMethod($order->shipping_method, $order->shipping_option) }}'"
                @endif
                :is_selected_shipping="true"
                :customer_order_numbers="{{ $customerOrderNumbers }}"
                :currency="{{ get_application_currency()->symbol }}"
        ></create-order>
    </div>
@stop

@push('header')
    <script>
        "use strict";

        window.trans = {
            "Order": "{{ __('Order') }}",
            "Order information": "{{ __('Order information') }}",
            "Create a new product": "{{ __('Create a new product') }}",
            "Out of stock": "{{ __('Out of stock') }}",
            "product(s) available": "{{ __('product(s) available') }}",
            "No product found!": "{{ __('No product found!') }}",
        }
    </script>
@endpush
