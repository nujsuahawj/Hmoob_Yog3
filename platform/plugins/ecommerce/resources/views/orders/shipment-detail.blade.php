<div class="shipment-info-panel hide-print">
    <div class="shipment-info-header">
        <a target="_blank" href="{{ route('ecommerce.shipments.edit', $shipment->id) }}">
            <h4>{{ get_shipment_code($shipment->id) }}</h4>
        </a>
        <span class="label carrier-status carrier-status-{{ $shipment->status }}">{{ $shipment->status->label() }}</span>
    </div>

    <div class="pd-all-20 pt10">
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ $shipment->order->shipping_method_name }}</span>
            </div>
        </div>
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ __('Warehouse') }}:</span> <span><i>{{ $shipment->store->name }}</i></span>
            </div>
            <div class="flexbox-grid-form-item rps-no-pd-none-r ws-nm">
                <span>{{ __('Weight (:unit)', ['unit' => ecommerce_weight_unit()]) }}:</span> <span><i>{{ $shipment->weight }} kg</i></span>
            </div>
        </div>
        <div class="flexbox-grid-form flexbox-grid-form-no-outside-padding rps-form-767 pt10">
            <div class="flexbox-grid-form-item ws-nm">
                <span>{{ __('Shipment code') }}:</span> @if (!$shipment->shipment_id) <span><i>{{ __('Unknown') }}</i></span> @else <a class="text-underline bold-light" style="font-size: 12px;" title="{{ $shipment->shipment_id }}" target="_blank" href="{{ OrderHelper::getShippingDetailUrl($shipment->order->shipping_method) }}"><i>{{ $shipment->shipment_id }}</i></a>@endif
            </div>
            <div class="flexbox-grid-form-item ws-nm rps-no-pd-none-r">
                <span>{{ __('Cash on delivery amount (COD)') }}:</span>
                <span><i>{{ format_price($shipment->cod_amount) }}</i></span>
            </div>
        </div>
    </div>
    @if ($shipment->status != \Botble\Ecommerce\Enums\ShippingStatusEnum::CANCELED)
        <div class="panel-heading order-bottom shipment-actions-wrapper">
            <div class="flexbox-grid-default">
                @if (in_array($shipment->status, [\Botble\Ecommerce\Enums\ShippingStatusEnum::NOT_APPROVED, \Botble\Ecommerce\Enums\ShippingStatusEnum::APPROVED]))
                    <div class="flexbox-content">
                        <button type="button" class="btn btn-secondary btn-destroy btn-cancel-shipment" data-action="{{ route('orders.cancel-shipment', $shipment->id) }}">{{ __('Cancel order') }}</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
