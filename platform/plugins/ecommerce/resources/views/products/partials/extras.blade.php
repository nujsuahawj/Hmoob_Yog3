@if ($onlyRelatedProducts)
    <div class="tabbable-custom tabbable-tabdrop">
        <div class="tab-content" style="border-radius: 0; border: none; padding: 20px;">
            <div class="form-group">
                <label>{{ __('Related products') }}</label>
                <input type="hidden" name="related_products" value="@if ($product) {{ implode(',', $product->products()->allRelatedIds()->toArray()) }} @endif" />
                <div class="box-search-advance product">
                    <div>
                        <input type="text" class="next-input textbox-advancesearch" placeholder="{{ __('Search products') }}" data-target="{{ route('products.get-list-product-for-search', $product ? $product->id : 0) }}">
                    </div>
                    <div class="panel panel-default">

                    </div>
                </div>
                @include('plugins/ecommerce::products.partials.selected-products-list', ['products' => $product ? $product->products : collect([]), 'includeVariation' => false])
            </div>
        </div>
    </div>
@else
    <div class="tabbable-custom tabbable-tabdrop">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#tab_6_2" class="nav-link active" data-toggle="tab"
                   aria-expanded="true">{{ __('Related products') }}</a>
            </li>
            <li class="nav-item">
                <a href="#tab_grouped_products_selector" class="nav-link" data-toggle="tab"
                   aria-expanded="false">{{ __('Grouped products') }}</a>
            </li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane fade show active" id="tab_6_2">
                <div class="form-group">
                    <label>{{ __('Related products') }}</label>
                    <input type="hidden" name="related_products" value="@if ($product) {{ implode(',', $product->products()->allRelatedIds()->toArray()) }} @endif" />
                    <div class="box-search-advance product">
                        <div>
                            <input type="text" class="next-input textbox-advancesearch" placeholder="{{ __('Search products') }}" data-target="{{ route('products.get-list-product-for-search', $product ? $product->id : 0) }}">
                        </div>
                        <div class="panel panel-default">

                        </div>
                    </div>
                    @include('plugins/ecommerce::products.partials.selected-products-list', ['products' => $product ? $product->products : collect([]), 'includeVariation' => false])
                </div>
                <hr>
                <div class="form-group">
                    <label>{{ __('Cross Sale products') }}</label>
                    <input type="hidden" name="cross_sale_products" value="@if ($product) {{ implode(',', $product->crossSales()->allRelatedIds()->toArray()) }} @endif"/>
                    <div class="box-search-advance product">
                        <div>
                            <input type="text" class="next-input textbox-advancesearch" placeholder="{{ __('Search products') }}" data-target="{{ route('products.get-list-product-for-search', $product ? $product->id : 0) }}">
                        </div>
                        <div class="panel panel-default">

                        </div>
                    </div>
                    @include('plugins/ecommerce::products.partials.selected-products-list', ['products' => $product ? $product->crossSales : collect([]), 'includeVariation' => false])
                </div>
                <hr>
                <div class="form-group">
                    <label>{{ __('Up Sale products') }}</label>
                    <input type="hidden" name="up_sale_products" value="@if ($product) {{ implode(',', $product->upSales()->allRelatedIds()->toArray()) }} @endif"/>
                    <div class="box-search-advance product">
                        <div>
                            <input type="text" class="next-input textbox-advancesearch" placeholder="{{ __('Search products') }}" data-target="{{ route('products.get-list-product-for-search', $product ? $product->id : 0) }}">
                        </div>
                        <div class="panel panel-default">

                        </div>
                    </div>
                    @include('plugins/ecommerce::products.partials.selected-products-list', ['products' => $product ? $product->upSales : collect([]), 'includeVariation' => false])
                </div>
            </div>
            <div class="tab-pane fade" id="tab_grouped_products_selector">
                <div class="form-group">
                    <input type="hidden" name="grouped_products" value="@if ($product) {{ implode(',', $product->groupedItems()->pluck('product_id')->all()) }} @endif"/>
                    <div class="box-search-advance product">
                        <div>
                            <input type="text" class="next-input textbox-advancesearch" placeholder="{{ __('Search products') }}" data-target="{{ route('products.get-list-product-for-search', $product ? $product->id : 0) }}">
                        </div>
                        <div class="panel panel-default">

                        </div>
                    </div>
                    @include('plugins/ecommerce::products.partials.selected-products-list', ['products' => $product ? $product->groupedProduct : collect([]), 'includeVariation' => false])
                </div>
            </div>

        </div>
    </div>

@endif

<script id="selected_product_list_template" type="text/x-custom-template">
    <tr>
        <td class="width-60-px min-width-60-px">
            <div class="wrap-img vertical-align-m-i">
                <img class="thumb-image" src="__image__" title="__name__"></div>
        </td>
        <td class="pl5 p-r5 min-width-200-px">
            <a class="hover-underline pre-line" href="__url__">__name__</a>
            <p class="type-subdued">__attributes__</p>
        </td>
        <td class="pl5 p-r5 text-right width-20-px min-width-20-px">
            <a href="#" class="btn-trigger-remove-selected-product" title="{{ __('Delete') }}" data-id="__id__">
                <i class="fa fa-times"></i>
            </a>
        </td>
    </tr>
</script>
