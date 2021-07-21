<div class="discount @if ($item->isExpired()) is-discount-disabled @endif">
    @if ($item->isExpired())
        <span class="discount-expired show">{{ __('Expired') }}</span>
    @endif
    <div class="discount-inner">
        <p class="discount-code"> @if ($item->type === 'coupon') {{ __('COUPON CODE') }}: <b>{{ $item->code }}</b> @else {{ __('DISCOUNT PROMOTION') }}: {{ $item->title }} @endif</p>
        <p class="discount-desc">
            {!! get_discount_description($item) !!}
        </p>
        @if ($item->type === 'coupon')
            <p class="@if (!$item->isExpired()) discount-text-color @else discount-desc @endif">({{ __('Coupon code') }} <b>@if ($item->can_use_with_promotion) {{ __('can') }} @else {{ __('cannot') }} @endif</b> {{ __('be used with promotion.') }})</p>
        @endif
    </div>
</div>
