<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class StoreProductService
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * StoreProductService constructor.
     * @param ProductInterface $product
     */
    public function __construct(ProductInterface $product)
    {
        $this->productRepository = $product;
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return Product|false|Model
     */
    public function execute(Request $request, Product $product)
    {
        $data = $request->input();

        $product->fill($data);

        $product->images = json_encode($request->input('images', []));

        if ($product->sale_price > $product->price) {
            $product->sale_price = null;
        }

        if ($product->sale_type == 0) {
            $product->start_date = null;
            $product->end_date = null;
        }

        /**
         * @var Product $product
         */
        $product = $this->productRepository->createOrUpdate($product);

        if (!$product->id) {
            event(new CreatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
        } else {
            event(new UpdatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
        }

        if ($product) {
            $product->categories()->sync($request->input('categories', []));

            $product->productCollections()->sync($request->input('product_collections', []));

            if ($request->has('related_products')) {
                $product->products()->sync(array_filter(explode(',', $request->input('related_products', ''))));
            }

            if ($request->has('cross_sale_products')) {
                $product->crossSales()->sync(array_filter(explode(',', $request->input('cross_sale_products', ''))));
            }

            if ($request->has('up_sale_products')) {
                $product->upSales()->sync(array_filter(explode(',', $request->input('up_sale_products', ''))));
            }
        }

        return $product;
    }
}
