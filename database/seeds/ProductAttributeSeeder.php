<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductAttributeSet::truncate();

        ProductAttributeSet::create([
            'title'                     => 'Color',
            'slug'                      => 'color',
            'display_layout'            => 'visual',
            'is_searchable'             => true,
            'is_use_in_product_listing' => true,
            'status'                    => BaseStatusEnum::PUBLISHED,
        ]);

        ProductAttributeSet::create([
            'title'                     => 'Size',
            'slug'                      => 'size',
            'display_layout'            => 'text',
            'is_searchable'             => true,
            'is_use_in_product_listing' => true,
            'status'                    => BaseStatusEnum::PUBLISHED,
        ]);

        ProductAttribute::truncate();

        $productAttributes = [
            [
                'attribute_set_id' => 1,
                'title'            => 'Green',
                'slug'             => 'green',
                'color'            => '#5FB7D4',
                'is_default'       => true,
                'order'            => 1,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 1,
                'title'            => 'Blue',
                'slug'             => 'blue',
                'color'            => '#333333',
                'is_default'       => false,
                'order'            => 2,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 1,
                'title'            => 'Red',
                'slug'             => 'red',
                'color'            => '#DA323F',
                'is_default'       => false,
                'order'            => 3,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 1,
                'title'            => 'Black',
                'slug'             => 'back',
                'color'            => '#2F366C',
                'is_default'       => false,
                'order'            => 4,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 1,
                'title'            => 'Brown',
                'slug'             => 'brown',
                'color'            => '#87554B',
                'is_default'       => false,
                'order'            => 5,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 2,
                'title'            => 'S',
                'slug'             => 's',
                'is_default'       => true,
                'order'            => 1,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 2,
                'title'            => 'M',
                'slug'             => 'm',
                'is_default'       => true,
                'order'            => 2,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 2,
                'title'            => 'L',
                'slug'             => 'l',
                'is_default'       => true,
                'order'            => 3,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 2,
                'title'            => 'XL',
                'slug'             => 'xl',
                'is_default'       => true,
                'order'            => 4,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
            [
                'attribute_set_id' => 2,
                'title'            => 'XXL',
                'slug'             => 'xxl',
                'is_default'       => true,
                'order'            => 5,
                'status'           => BaseStatusEnum::PUBLISHED,
            ],
        ];

        foreach ($productAttributes as $item) {
            ProductAttribute::create($item);
        }
    }
}
