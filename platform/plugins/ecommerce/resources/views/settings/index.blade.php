@extends('core/base::layouts.master')
@section('content')
    {!! Form::open(['url' => route('ecommerce.settings'), 'class' => 'main-setting-form']) !!}
        <div class="max-width-1200">
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ __('Basic information') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::store-locator.description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <label class="text-title-field" for="store_name">{{ trans('plugins/ecommerce::store-locator.shop_name') }}</label>
                            <input type="text" class="next-input" name="store_name" id="store_name" value="{{ get_ecommerce_setting('store_name') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="store_phone">{{ trans('plugins/ecommerce::store-locator.phone') }}</label>
                            <input type="text" class="next-input" name="store_phone" id="store_phone" value="{{ get_ecommerce_setting('store_phone') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="store_address">{{ trans('plugins/ecommerce::store-locator.address') }}</label>
                            <input type="text" class="next-input" name="store_address" id="store_address" value="{{ get_ecommerce_setting('store_address') }}">
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_state">{{ __('State') }}</label>
                                <input type="text" class="next-input" name="store_state" id="store_state" value="{{ get_ecommerce_setting('store_state') }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="text-title-field" for="store_city">{{ __('City') }}</label>
                                <input type="text" class="next-input" name="store_city" id="store_city" value="{{ get_ecommerce_setting('store_city') }}">
                            </div>
                        </div>
                        <div class="form-group mb0">
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
                </div>
            </div>
            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::ecommerce.standard_and_format') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::ecommerce.standard_and_format_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <label class="next-label">{{ trans('plugins/ecommerce::ecommerce.change_order_format') }}</label>
                        <p class="type-subdued">{{ trans('plugins/ecommerce::ecommerce.change_order_format_description', ['number' => config('plugins.ecommerce.order.default_order_start_number')]) }}</p>
                        <div class="form-group row">
                            <div class="col-sm-6 p-none-l">
                                <label class="text-title-field" for="store_order_prefix">{{ trans('plugins/ecommerce::ecommerce.start_with') }}</label>
                                <div class="next-input--stylized">
                                    <span class="next-input-add-on next-input__add-on--before">#</span>
                                    <input type="text" class="next-input next-input--invisible" name="store_order_prefix" id="store_order_prefix" value="{{ get_ecommerce_setting('store_order_prefix') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 p-none-r rps-p-l-none">
                                <label class="text-title-field" for="store_order_suffix">{{ trans('plugins/ecommerce::ecommerce.end_with') }}</label>
                                <input type="text" class="next-input" name="store_order_suffix" id="store_order_suffix" value="{{ get_ecommerce_setting('store_order_suffix') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <p class="setting-note mb0">{{ trans('plugins/ecommerce::ecommerce.order_will_be_shown') }} <span class="sample-order-code">#<span class="sample-order-code-prefix">{{ get_ecommerce_setting('store_order_prefix') ? get_ecommerce_setting('store_order_prefix') . '-' : '' }}</span>{{ config('plugins.ecommerce.order.default_order_start_number') }}<span class="sample-order-code-suffix">{{ get_ecommerce_setting('store_order_suffix') ? '-' . get_ecommerce_setting('store_order_suffix') : '' }}</span></span> </p>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6 p-none-l">
                                <label class="text-title-field" for="store_weight_unit">{{ trans('plugins/ecommerce::ecommerce.weight_unit') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="store_weight_unit" id="store_weight_unit">
                                        <option value="g" @if (get_ecommerce_setting('store_weight_unit', 'g') === 'g') selected @endif>{{ __('Gram (g)') }}</option>
                                        <option value="kg" @if (get_ecommerce_setting('store_weight_unit', 'g') === 'kg') selected @endif>{{ __('Kilogram (kg)') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-sm-6 p-none-r rps-p-l-none">
                                <label class="text-title-field" for="store_width_height_unit">{{ trans('plugins/ecommerce::ecommerce.height_unit') }}</label>
                                <div class="ui-select-wrapper">
                                    <select class="ui-select" name="store_width_height_unit" id="store_width_height_unit">
                                        <option value="cm" @if (get_ecommerce_setting('store_width_height_unit', 'cm') === 'cm') selected @endif>{{ __('Centimeter (cm)') }}</option>
                                        <option value="m" @if (get_ecommerce_setting('store_width_height_unit', 'cm') === 'm') selected @endif>{{ __('Meter (m)') }}</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ trans('plugins/ecommerce::currency.currencies') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ trans('plugins/ecommerce::currency.setting_description') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                    <textarea name="currencies"
                              id="currencies"
                              class="hidden">{!! json_encode($currencies) !!}</textarea>
                        <textarea name="deleted_currencies"
                                  id="deleted_currencies"
                                  class="hidden"></textarea>
                        <div class="swatches-container">
                            <div class="header clearfix">
                                <div class="swatch-item">
                                    {{ trans('plugins/ecommerce::currency.name') }}
                                </div>
                                <div class="swatch-item">
                                    {{ trans('plugins/ecommerce::currency.symbol') }}
                                </div>
                                <div class="swatch-item swatch-decimals">
                                    {{ trans('plugins/ecommerce::currency.number_of_decimals') }}
                                </div>
                                <div class="swatch-item swatch-exchange-rate">
                                    {{ trans('plugins/ecommerce::currency.exchange_rate') }}
                                </div>
                                <div class="swatch-item swatch-is-prefix-symbol">
                                    {{ trans('plugins/ecommerce::currency.is_prefix_symbol') }}
                                </div>
                                <div class="swatch-is-default">
                                    {{ trans('plugins/ecommerce::currency.is_default') }}
                                </div>
                                <div class="remove-item">{{ trans('plugins/ecommerce::currency.remove') }}</div>
                            </div>
                            <ul class="swatches-list">

                            </ul>
                            <a href="#" class="js-add-new-attribute">
                                {{ trans('plugins/ecommerce::currency.new_currency') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section store-locator-wrap">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ __('Store locators') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ __('All the lists of your chains, main stores, branches, etc. The locations can be used to track sales and to help us configure tax rates to charge when selling products.') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <table class="table table-striped table-bordered table-header-color">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Primary?') }}</th>
                                    <th style="width: 120px;" class="text-right">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($storeLocators as $storeLocator)
                                <tr>
                                    <td>
                                        {{ $storeLocator->name }}
                                    </td>
                                    <td>
                                        {{ $storeLocator->phone }}
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $storeLocator->email }}">{{ $storeLocator->email }}</a>
                                    </td>
                                    <td>
                                        <span>{{ $storeLocator->address }}</span>,
                                        <span>{{ $storeLocator->city }}</span>,
                                        <span>{{ $storeLocator->state }}</span>,
                                        <span>{{ $storeLocator->country_name }}</span>
                                    </td>
                                    <td>
                                        {{ $storeLocator->is_primary ? trans('core/base::base.yes') : trans('core/base::base.no') }}
                                    </td>
                                    <td class="text-right">
                                        @if (!$storeLocator->is_primary && $storeLocators->count() > 1)
                                            <button class="btn btn-danger btn-small btn-trigger-delete-store-locator" data-target="{{ route('ecommerce.store-locators.destroy', $storeLocator->id) }}" type="button">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-small btn-trigger-show-store-locator" data-type="update" data-load-form="{{ route('ecommerce.store-locators.form', $storeLocator->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <a href="#" class="btn btn-primary btn-trigger-show-store-locator" data-type="create" data-load-form="{{ route('ecommerce.store-locators.form') }}">
                            {{ __('Add new') }}
                        </a>
                        @if (count($storeLocators) > 0)
                            <p style="margin-top: 10px">{{ __('Or') }} <a href="#" data-toggle="modal" data-target="#change-primary-store-locator-modal">{{ __('change default store locator') }}</a></p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section store-locator-wrap">
                <div class="flexbox-annotated-section-annotation">
                    <div class="annotated-section-title pd-all-20">
                        <h2>{{ __('Other settings') }}</h2>
                    </div>
                    <div class="annotated-section-description pd-all-20 p-none-t">
                        <p class="color-note">{{ __('Settings for cart, review...') }}</p>
                    </div>
                </div>
                <div class="flexbox-annotated-section-content">
                    <div class="wrapper-content pd-all-20">
                        <div class="form-group">
                            <label class="text-title-field"
                                   for="shopping_cart_enabled">{{ __('Enable shopping cart?') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="shopping_cart_enabled" class="hrv-radio"
                                       value="1"
                                       @if (get_ecommerce_setting('shopping_cart_enabled', '1')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="shopping_cart_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!get_ecommerce_setting('shopping_cart_enabled', '1')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="text-title-field"
                                   for="review_enabled">{{ __('Enable review?') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="review_enabled" class="hrv-radio"
                                       value="1"
                                       @if (get_ecommerce_setting('review_enabled', '1')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                            </label>
                            <label class="hrv-label">
                                <input type="radio" name="review_enabled" class="hrv-radio"
                                       value="0"
                                       @if (!get_ecommerce_setting('review_enabled', '1')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">
                    &nbsp;
                </div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('plugins/ecommerce::currency.save_settings') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    {!! Form::modalAction('add-store-locator-modal', __('Add location'), 'info', view('plugins/ecommerce::settings.store-locator-item', ['locator' => null])->render(), 'add-store-locator-button', __('Save location'), 'modal-md') !!}
    {!! Form::modalAction('update-store-locator-modal', __('Edit location'), 'info', view('plugins/ecommerce::settings.store-locator-item', ['locator' => null])->render(), 'update-store-locator-button', __('Save location'), 'modal-md') !!}
    {!! Form::modalAction('delete-store-locator-modal', __('Delete location'), 'info', __('Are you sure you want to delete this location? This action cannot be undo.'), 'delete-store-locator-button', __('Accept')) !!}
    {!! Form::modalAction('change-primary-store-locator-modal', __('Change primary location'), 'info', view('plugins/ecommerce::settings.store-locator-change-primary', compact('storeLocators'))->render(), 'change-primary-store-locator-button', __('Accept'), 'modal-sm') !!}
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/ecommerce::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/ecommerce::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
