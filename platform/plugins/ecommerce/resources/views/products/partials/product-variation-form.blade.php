<div class="variation-form-wrapper">
    <div class="row">
        @foreach ($productAttributeSets as $attributeSet)
            @if ($attributeSet->is_selected)
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="attribute-{{ $attributeSet->slug }}" class="text-title-field required">{{ $attributeSet->title }}</label>
                        <div class="ui-select-wrapper">
                            <select class="ui-select" id="attribute-{{ $attributeSet->slug }}" name="attribute_sets[{{ $attributeSet->id }}]">
                                @foreach ($productAttributes->where('attribute_set_id', $attributeSet->id) as $attribute)
                                    <option value="{{ $attribute->id }}" @if ($productVariationsInfo && $productVariationsInfo->where('attribute_set_id', $attributeSet->id)->where('id', $attribute->id)->first()) selected @endif>
                                        {{ $attribute->title }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @include('plugins/ecommerce::products.partials.general', compact('product', 'originalProduct'))
    <div class="variation-images" style="position: relative; border: 1px dashed #ccc; padding: 10px;">
        @include('plugins/ecommerce::products.partials.images', compact('product'))
    </div>
</div>
