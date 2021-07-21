<?php

use Botble\Ecommerce\Models\Customer;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Customer::truncate();

        Customer::create([
            'name'     => 'John Smith',
            'email'    => 'john.smith@botble.com',
            'password' => bcrypt('12345678'),
            'phone'    => $faker->phoneNumber,
        ]);

        for ($i = 0; $i < 20; $i++) {
            Customer::create([
                'name'     => $faker->name,
                'email'    => $faker->safeEmail,
                'password' => bcrypt('12345678'),
                'phone'    => $faker->phoneNumber,
            ]);
        }
    }
}
