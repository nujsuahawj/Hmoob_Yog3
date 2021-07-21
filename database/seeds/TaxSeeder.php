<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tax::truncate();

        Tax::create([
            'title'      => 'VAT',
            'percentage' => 10,
            'priority'   => 1,
            'status'     => BaseStatusEnum::PUBLISHED,
        ]);
    }
}
