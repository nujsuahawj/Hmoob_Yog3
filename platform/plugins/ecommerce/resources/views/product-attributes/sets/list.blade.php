<script id="product_attribute_template" type="text/x-custom-template">
    <li data-id="__id__" class="clearfix">
        <div class="swatch-is-default">
            <input type="radio" name="related_attribute_is_default" value="__position__" __checked__>
        </div>
        <div class="swatch-title">
            <input type="text" class="form-control" value="__title__">
        </div>
        <div class="swatch-slug">
            <input type="text" class="form-control" value="__slug__">
        </div>
        <div class="swatch-value">
            <input type="text" class="form-control input-color-picker" value="__color__">
        </div>
        <div class="swatch-image">
            <div class="image-box-container">
                @include('plugins/ecommerce::components.form.image', [
                    'name' => '',
                    'value' => '__image__',
                    'thumb' => RvMedia::getDefaultImage(false),
                ])
            </div>
        </div>
        <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
    </li>
</script>
<textarea name="attributes" id="attributes" class="hidden">{!! json_encode($attributes) !!}</textarea>
<textarea name="deleted_attributes" id="deleted_attributes" class="hidden"></textarea>
<div class="swatches-container">
    <div class="header clearfix">
        <div class="swatch-is-default">
            {{ __('Is default') }}
        </div>
        <div class="swatch-title">
            {{ __('Title') }}
        </div>
        <div class="swatch-slug">
            {{ __('Slug') }}
        </div>
        <div class="swatch-value">
            {{ __('Color') }}
        </div>
        <div class="swatch-image">
            {{ __('Image') }}
        </div>
        <div class="remove-item">{{ __('Remove') }}</div>
    </div>
    <ul class="swatches-list">
        @if (count($attributes) > 0)
            @foreach($attributes as $attribute)
                <li data-id="{{ $attribute['id'] }}" class="clearfix">
                    <div class="swatch-is-default">
                        <input type="radio" name="related_attribute_is_default" value="{{ $attribute['order'] }}" @if ($attribute['is_default']) checked @endif>
                    </div>
                    <div class="swatch-title">
                        <input type="text" class="form-control" value="{{ $attribute['title'] }}">
                    </div>
                    <div class="swatch-slug">
                        <input type="text" class="form-control" value="{{ $attribute['slug'] }}">
                    </div>
                    <div class="swatch-value">
                        <input type="text" class="form-control input-color-picker" value="{{ $attribute['color'] }}">
                    </div>
                    <div class="swatch-image">
                        <div class="image-box-container">
                            @include('plugins/ecommerce::components.form.image', [
                                'name' => '',
                                'value' => $attribute['image'],
                                'thumb' => $attribute['image'] ? RvMedia::getImageUrl($attribute['image'], 'thumb') : RvMedia::getDefaultImage(false),
                            ])
                        </div>
                    </div>
                    <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
                </li>
            @endforeach
        @endif
    </ul>
    <button type="button" class="btn purple js-add-new-attribute">{{ __('Add new attribute') }}</button>
</div>
