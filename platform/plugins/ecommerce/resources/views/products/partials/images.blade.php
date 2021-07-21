<div class="product-images-wrapper">
    <a href="#" class="add-new-product-image js-btn-trigger-add-image"
       data-name="images[]">{{ trans('plugins/ecommerce::products.control.button_add_image') }}
    </a>
    @php $productImages = old('images', isset($product) ? $product->images : null); @endphp
    <div class="images-wrapper">
        <div data-name="images[]" class="text-center cursor-pointer js-btn-trigger-add-image default-placeholder-product-image @if (is_array($productImages) && !empty($productImages)) hidden @endif">
            <img src="{{ RvMedia::getDefaultImage(false) }}" alt="{{ __('Image') }}" width="120">
            <br>
            <p style="color:#c3cfd8">{{ __('Using button') }} <strong>{{ __('Select image') }}</strong> {{ __('to add more images') }}.</p>
        </div>
        <ul class="list-unstyled list-gallery-media-images clearfix @if (!is_array($productImages) || empty($productImages)) hidden @endif" style="padding-top: 20px;">
            @if (is_array($productImages) && !empty($productImages))
                @foreach($productImages as $image)
                    <li class="product-image-item-handler">
                        @include('plugins/ecommerce::components.form.image', [
                            'name' => 'images[]',
                            'value' => $image,
                            'thumb' => RvMedia::getImageUrl($image, 'thumb')
                        ])
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>
