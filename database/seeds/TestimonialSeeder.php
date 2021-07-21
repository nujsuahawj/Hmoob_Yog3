<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Testimonial\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testimonials = [
            [
                'name'    => 'Donald H. Hilixer',
                'company' => 'Founder, Hilix',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.',
                'image'   => 'testimonials/1.jpg',
                'status'  => BaseStatusEnum::PUBLISHED,
            ],
            [
                'name'    => 'Rosalina D. William',
                'company' => 'Founder, qux co.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.',
                'image'   => 'testimonials/2.jpg',
                'status'  => BaseStatusEnum::PUBLISHED,
            ],
            [
                'name'    => 'John Becker',
                'company' => 'CEO, Highlands coffee',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.',
                'image'   => 'testimonials/3.jpg',
                'status'  => BaseStatusEnum::PUBLISHED,
            ],
            [
                'name'    => 'Browfish Dumble',
                'company' => 'Founder, Condo',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.',
                'image'   => 'testimonials/4.jpg',
                'status'  => BaseStatusEnum::PUBLISHED,
            ],
        ];

        Testimonial::truncate();

        foreach ($testimonials as $item) {
            Testimonial::create($item);
        }
    }
}
