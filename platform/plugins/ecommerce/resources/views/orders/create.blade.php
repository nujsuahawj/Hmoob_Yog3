@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order currency="{{ get_application_currency()->symbol }}"></create-order>
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
