{!! Form::open(['url' => route('orders.update-shipping-address', $address->id ?? 0)]) !!}
    <input type="hidden" name="order_id" value="{{ $orderId }}">
    <div class="next-form-section">
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('Name') }}</label>
                <input type="text" class="next-input" name="name" placeholder="{{ __('Name') }}" value="{{ $address->name }}">
            </div>
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('Phone') }}</label>
                <input type="text" class="next-input" name="phone" placeholder="{{ __('Phone') }}" value="{{ $address->phone }}">
            </div>
        </div>
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('Email (optional)') }}</label>
                <input type="text" class="next-input" name="email" placeholder="{{ __('Email') }}" value="{{ $address->email }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('Country') }}</label>
                <select name="country" class="form-control" >
                    @foreach(['' => __('Select country...')] + \Botble\Base\Supports\Helper::countries() as $countryCode => $countryName)
                        <option value="{{ $countryCode }}" @if ($address->country == $countryCode) selected @endif>{{ $countryName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('State') }}</label>
                <input type="text" class="next-input" name="state" placeholder="{{ __('State') }}" value="{{ $address->state }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('City') }}</label>
                <input type="text" class="next-input" name="city" placeholder="{{ __('City') }}" value="{{ $address->city }}">
            </div>
        </div>

        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">{{ __('Address') }}</label>
                <input type="text" class="next-input" name="address" placeholder="{{ __('Address') }}" value="{{ $address->address }}">
            </div>
        </div>

    </div>
{!! Form::close() !!}
