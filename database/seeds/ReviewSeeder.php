<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Review;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Review::truncate();

        for ($i = 0; $i < 50; $i++) {
            Review::create([
                'product_id'  => $faker->numberBetween(1, 10),
                'customer_id' => $faker->numberBetween(1, 20),
                'star'        => $faker->numberBetween(1, 5),
                'comment'     => $faker->realText(20),
                'status'      => BaseStatusEnum::PUBLISHED,
            ]);
        }
    }
}
