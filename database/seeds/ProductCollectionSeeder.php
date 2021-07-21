<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCollection;
use Illuminate\Database\Seeder;

class ProductCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productCollections = [
            [
                'name'   => 'New Arrival',
                'slug'   => 'new-arrival',
                'status' => BaseStatusEnum::PUBLISHED,
            ],
            [
                'name'   => 'Best Sellers',
                'slug'   => 'best-sellers',
                'status' => BaseStatusEnum::PUBLISHED,
            ],
            [
                'name'   => 'Special Offer',
                'slug'   => 'special-offer',
                'status' => BaseStatusEnum::PUBLISHED,
            ],
        ];

        ProductCollection::truncate();

        foreach ($productCollections as $item) {
            ProductCollection::create($item);
        }
    }
}
