<div id="product-variations-wrapper">
    <div class="variation-actions">
        <a href="#" class="btn-trigger-select-product-attributes" data-target="{{ route('products.store-related-attributes', $product->id) }}">{{ __('Edit attribute') }}</a>
        <a href="#" class="btn-trigger-add-new-product-variation" data-target="{{ route('products.add-version', $product->id) }}">{{ __('Add variation') }}</a>
        <a href="#" class="btn-trigger-generate-all-versions" data-target="{{ route('products.generate-all-versions', $product->id) }}">{{ __('Generate all variations') }}</a>
    </div>
    @if (!$productVariations->isEmpty())
        <table class="table table-hover-variants">
            <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                @foreach ($productAttributeSets->where('is_selected', '<>', null)->whereIn('id', $productVariationsInfo->pluck('attribute_set_id')->all()) as $attributeSet)
                        <th>{{ $attributeSet->title }}</th>
                @endforeach
                @foreach ($productAttributeSets->where('is_selected', '<>', null)->whereNotIn('id', $productVariationsInfo->pluck('attribute_set_id')->all()) as $attributeSet)
                    <th>{{ $attributeSet->title }}</th>
                @endforeach
                <th>{{ __('Price') }}</th>
                <th>{{ __('Is default?') }}</th>
                <th class="text-center">{{ __('Action') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($productVariations as $variation)
                @php
                    $currentRelatedProduct = $productsRelatedToVariation->where('variation_id', $variation->id)->first();
                @endphp
                <tr>
                    <td>
                        <div class="wrap-img-product">
                            <img src="{{ RvMedia::getImageUrl($currentRelatedProduct && $currentRelatedProduct->image ? $currentRelatedProduct->image : $product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ __('Variation image')  }}">
                        </div>
                    </td>
                    @foreach ($productVariationsInfo->where('variation_id', $variation->id) as $key => $item)
                        <td>{{ $item->title }}</td>
                    @endforeach
                    @for($index = 0; $index < ($productAttributeSets->where('is_selected', '<>', null)->count() - $productVariationsInfo->where('variation_id', $variation->id)->count()); $index++)
                        <td>--</td>
                    @endfor
                    <td>{{ $currentRelatedProduct ? format_price($currentRelatedProduct->price) : format_price($product->price) }}</td>
                    <td>
                        <label>
                            <input type="radio" class="hrv-radio"
                                   {{ $variation->is_default ? 'checked' : '' }}
                                   name="variation_default_id"
                                   value="{{ $variation->id }}">
                        </label>
                    </td>
                    <td style="width: 180px;" class="text-center">
                        <a href="#" class="btn btn-info btn-trigger-edit-product-version"
                                data-target="{{ route('products.update-version', [$variation->id]) }}"
                                data-load-form="{{ route('products.get-version-form', [$variation->id]) }}"
                        >{{ __('Edit') }}</a>
                        <a href="#" data-target="{{ route('products.delete-version', [$variation->id]) }}"
                           class="btn-trigger-delete-version btn btn-danger">{{ __('Delete') }}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>{{ __('Click on "Edit attribute" to add/remove attributes of variation or click on "Add new variation" to add variation.') }}</p>
    @endif
    {!! Form::modalAction('select-attribute-sets-modal', __('Select attribute'), 'info', view('plugins/ecommerce::products.partials.attribute-sets', compact('productAttributeSets'))->render(), 'store-related-attributes-button', __('Save changes')) !!}
    {!! Form::modalAction('add-new-product-variation-modal', __('Add new variation'), 'info', view('plugins/ecommerce::products.partials.product-variation-form', ['productAttributeSets' => $productAttributeSets, 'productAttributes' => $productAttributes, 'product' => null, 'originalProduct' => $product, 'productVariationsInfo' => null])->render(), 'store-product-variation-button', __('Save changes')) !!}
    {!! Form::modalAction('edit-product-variation-modal', __('Edit variation'), 'info', view('plugins/ecommerce::products.partials.product-variation-form', ['productAttributeSets' => $productAttributeSets, 'productAttributes' => $productAttributes, 'product' => null, 'originalProduct' => $product, 'productVariationsInfo' => null])->render(), 'update-product-variation-button', __('Save changes')) !!}
    {!! Form::modalAction('generate-all-versions-modal', __('Generate all variations'), 'info', __('Are you sure you want to generate all variations for this product?'), 'generate-all-versions-button', __('Continue')) !!}
    {!! Form::modalAction('confirm-delete-version-modal', __('Delete variation?'), 'danger', __('Are you sure you want to delete this variation? This action cannot be undo.'), 'delete-version-button', __('Continue')) !!}
</div>
