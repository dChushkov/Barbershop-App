<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Haircut',
                'description' => 'Classic haircut with clippers and scissors',
                'base_price' => 25.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Beard Trim',
                'description' => 'Professional beard trimming and shaping',
                'base_price' => 15.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Hot Towel Shave',
                'description' => 'Traditional hot towel shave with straight razor',
                'base_price' => 30.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Haircut & Beard',
                'description' => 'Complete grooming package including haircut and beard trim',
                'base_price' => 35.00,
                'duration_minutes' => 60,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
} 