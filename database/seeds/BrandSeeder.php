<?php

use Botble\Ecommerce\Models\Brand;
use Botble\Slug\Models\Slug;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            [
                'name'        => 'Fashion live',
                'logo'        => 'brands/1.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Hand crafted',
                'logo'        => 'brands/2.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Mestonix',
                'logo'        => 'brands/3.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Sunshine',
                'logo'        => 'brands/4.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Pure',
                'logo'        => 'brands/5.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Anfold',
                'logo'        => 'brands/6.png',
                'is_featured' => true,
            ],
            [
                'name'        => 'Automotive',
                'logo'        => 'brands/7.png',
                'is_featured' => true,
            ],
        ];

        Brand::truncate();
        Slug::where('reference_type', Brand::class)->delete();

        foreach ($brands as $key => $item) {
            $item['order'] = $key;
            $brand = Brand::create($item);

            Slug::create([
                'reference_type' => Brand::class,
                'reference_id'   => $brand->id,
                'key'            => Str::slug($brand->name),
                'prefix'         => SlugHelper::getPrefix(Brand::class),
            ]);
        }
    }
}
