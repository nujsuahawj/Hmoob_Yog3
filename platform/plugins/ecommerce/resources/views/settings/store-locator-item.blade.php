{!! Form::open(['url' => $locator ? route('ecommerce.store-locators.edit.post', $locator->id) : route('ecommerce.store-locators.create')]) !!}
<div class="next-form-section">
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field">{{ __('Store name') }}</label>
            <input type="text" class="next-input" name="name" placeholder="{{ __('Store name') }}" value="{{ $locator ? $locator->name : null }}">
        </div>
        <div class="next-form-grid-cell">
            <label class="text-title-field">{{ __('Phone') }}</label>
            <input type="text" class="next-input" name="phone" placeholder="{{ __('Phone') }}" value="{{ $locator ? $locator->phone : null }}">
        </div>
    </div>
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field">{{ __('Email') }}</label>
            <input type="text" class="next-input" name="email" placeholder="{{ __('Email') }}" value="{{ $locator ? $locator->email : null }}">
        </div>
    </div>
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field">{{ __('Address') }}</label>
            <input type="text" class="next-input" name="address" placeholder="{{ __('Address') }}" value="{{ $locator ? $locator->address : null}}">
        </div>
    </div>
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field" for="store_state">{{ __('State') }}</label>
            <input type="text" class="next-input" name="state" id="store_state" value="{{ get_ecommerce_setting('store_state') }}">
        </div>
        <div class="next-form-grid-cell">
            <label class="text-title-field" for="store_city">{{ __('City') }}</label>
            <input type="text" class="next-input" name="city" id="store_city" value="{{ get_ecommerce_setting('store_city') }}">
        </div>
    </div>
    <div class="next-form-grid">
        <div class="next-form-grid-cell">
            <label class="text-title-field" for="store_country">{{ __('Country') }}</label>
            <div class="ui-select-wrapper">
                <select name="store_country" class="ui-select" id="store_country">
                    @foreach(['' => __('Select country...')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                        <option value="{{ $countryCode }}" @if (get_ecommerce_setting('store_country') == $countryCode) selected @endif>{{ $countryName }}</option>
                    @endforeach
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="next-label">

            <input type="checkbox" class="hrv-checkbox" value="1" name="is_shipping_location" @if (!$locator || $locator->is_shipping_location) checked @endif>

            {{ __('Default shipping location') }}?
        </label>
    </div>
</div>
{!! Form::close() !!}
