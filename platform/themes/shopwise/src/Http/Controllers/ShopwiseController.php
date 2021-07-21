<?php

namespace Theme\Shopwise\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Testimonial\Repositories\Interfaces\TestimonialInterface;
use Botble\Theme\Http\Controllers\PublicController;
use Cart;
use Theme;
use Theme\Shopwise\Http\Resources\BrandResource;
use Theme\Shopwise\Http\Resources\PostResource;
use Theme\Shopwise\Http\Resources\ProductCategoryResource;
use Theme\Shopwise\Http\Resources\TestimonialResource;

class ShopwiseController extends PublicController
{
    /**
     * {@inheritDoc}
     */
    public function getIndex(BaseHttpResponse $response)
    {
        return parent::getIndex($response);
    }

    /**
     * {@inheritDoc}
     */
    public function getView(BaseHttpResponse $response, $key = null)
    {
        return parent::getView($response, $key);
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteMap()
    {
        return parent::getSiteMap();
    }

    /**
     * @param int $productCollectionId
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProducts($productCollectionId, BaseHttpResponse $response)
    {
        $products = get_products_by_collections([
            'collections' => [
                'by'       => 'id',
                'value_in' => [$productCollectionId],
            ],
            'take'        => 10,
            'with'        => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getFeaturedProductCategories(BaseHttpResponse $response)
    {
        $categories = get_featured_product_categories();

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTrendingProducts(BaseHttpResponse $response)
    {
        $products = get_trending_products([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedBrands(BaseHttpResponse $response)
    {
        $brands = get_featured_brands();

        return $response->setData(BrandResource::collection($brands));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedProducts(BaseHttpResponse $response)
    {
        $data = [];

        $products = get_featured_products([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        foreach ($products->chunk(3) as $chunk) {
            $item = '';
            foreach ($chunk as $product) {
                $item .= Theme::partial('product-item', compact('product'));
            }
            $data[] = $item;
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTopRatedProducts(BaseHttpResponse $response)
    {
        $products = get_top_rated_products(10, [
            'slugable',
            'variations',
            'productCollections',
            'variationAttributeSwatchesForProductList',
            'promotions',
        ]);

        $data = [];
        foreach ($products->chunk(3) as $chunk) {
            $item = '';
            foreach ($chunk as $product) {
                $item .= Theme::partial('product-item', compact('product'));
            }
            $data[] = $item;
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetOnSaleProducts(BaseHttpResponse $response)
    {
        $products = get_products_on_sale([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products->chunk(3) as $chunk) {
            $item = '';
            foreach ($chunk as $product) {
                $item .= Theme::partial('product-item', compact('product'));
            }
            $data[] = $item;
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxCart(BaseHttpResponse $response)
    {
        return $response->setData([
            'count' => Cart::instance('cart')->count(),
            'html'  => Theme::partial('cart'),
        ]);
    }

    /**
     * @return mixed
     */
    public function getQuickView($id)
    {
        $product = get_products([
            'condition' => [
                'ec_products.id'     => $id,
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => 1,
            'with'      => [
                'defaultProductAttributes',
                'slugable',
                'tags',
                'tags.slugable',
            ],
        ]);

        if (!$product) {
            abort(404);
        }

        return Theme::partial('quick-view', compact('product'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetFeaturedPosts(BaseHttpResponse $response)
    {
        $posts = app(PostInterface::class)->getFeatured(3);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    /**
     * @param BaseHttpResponse $response
     * @param TestimonialInterface $testimonialRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetTestimonials(BaseHttpResponse $response, TestimonialInterface $testimonialRepository)
    {
        $testimonials = $testimonialRepository->allBy(['status' => BaseStatusEnum::PUBLISHED]);

        return $response->setData(TestimonialResource::collection($testimonials));
    }
}
