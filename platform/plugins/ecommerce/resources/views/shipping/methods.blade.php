@extends('core/base::layouts.master')

@section('content')
        <div class="max-width-1200">
            <div class="group">
                <div class="row">
                    <div class="@if (count($shipping) > 0) col-md-3 col-sm-12 @else col-sm-12 @endif">
                        <h4>{{ __('Shipping Rules') }}</h4>
                        <p>{{ __('Rules to calculate shipping fee.') }}</p>
                        <p><a href="#" class="btn btn-secondary" data-fancybox data-type="ajax" data-src="{{ route('shipping_methods.region.create') }}">{{ __('Select country') }}</a></p>
                    </div>
                    <div class="col-md-9 col-sm-12">
                        <div class="wrapper-content">
                            <div class="table-wrap">
                                @foreach ($shipping as $shippingItem)
                                    <div class="wrap-table-shipping-{{ $shippingItem->id }}">
                                        <div class="pd-all-20 p-none-b">
                                            <label class="p-none-r">{{ __('Country') }}: <strong>{{ Arr::get(\Botble\Base\Supports\Helper::countries(), $shippingItem->title, $shippingItem->title) }}</strong></label>
                                            <a href="#" class="btn-change-link float-right pl20 btn-add-shipping-rule-trigger" data-shipping-id="{{ $shippingItem->id }}">{{ __('Add shipping rule') }}</a>
                                            &nbsp;&nbsp;&nbsp;
                                            <a href="#" class="btn-change-link float-right excerpt btn-confirm-delete-region-item-modal-trigger text-danger" data-id="{{ $shippingItem->id }}" data-name="{{ $shippingItem->title }}">{{ __('Delete') }}</a>
                                        </div>
                                        <div class="pd-all-20 p-none-t p-b10 border-bottom">
                                            @foreach($shippingItem->rules as $rule)
                                                @include('plugins/ecommerce::shipping.rule-item', compact('rule', 'shippingItem'))
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    {!! Form::modalAction('confirm-delete-price-item-modal', __('Delete shipping rate for area'), 'info', __('Are you sure you want to delete <strong class="region-price-item-label"></strong> from this shipping area?'), 'confirm-delete-price-item-button', __('Confirm'), 'modal-xs') !!}
    {!! Form::modalAction('confirm-delete-region-item-modal', __('Delete shipping area'), 'info', __('Are you sure you want to delete shipping area <strong class="region-item-label"></strong>?'), 'confirm-delete-region-item-button', __('Confirm'), 'modal-xs') !!}
    {!! Form::modalAction('confirm-delete-shipping-method-item-modal', __('Disconnect shipping provider'), 'info', __('Your site will be disconnected from <strong class="shipping-method-item-label"></strong>. Are you sure you want to continue?'), 'confirm-delete-shipping-method-item-button', __('Confirm'), 'modal-xs') !!}
    {!! Form::modalAction('add-shipping-rule-item-modal', __('Add shipping fee for area'), 'info', view('plugins/ecommerce::shipping.rule-item', ['rule' => null])->render(), 'add-shipping-rule-item-button', __('Save')) !!}
    <div data-delete-region-item-url="{{ route('shipping_methods.region.destroy') }}"></div>
    <div data-delete-rule-item-url="{{ route('shipping_methods.region.rule.destroy') }}"></div>
    <div data-delete-shipping-method-item-url="{{ route('shipping_methods.delete_method') }}"></div>
@stop
