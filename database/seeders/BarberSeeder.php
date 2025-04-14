<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barber;
use App\Models\Service;
use App\Models\WorkingHour;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barbers = [
            [
                'name' => 'John Smith',
                'bio' => 'Experienced barber with over 10 years of experience in classic and modern haircuts.',
                'photo' => '/images/barbers/barber1.jpg',
            ],
            [
                'name' => 'Mike Johnson',
                'bio' => 'Specialized in beard grooming and styling. Expert in traditional barbering techniques.',
                'photo' => '/images/barbers/barber2.jpg',
            ],
            [
                'name' => 'David Wilson',
                'bio' => 'Young and talented barber with fresh ideas and modern approach to men\'s grooming.',
                'photo' => '/images/barbers/barber3.jpg',
            ],
        ];

        $workingHours = [
            [
                'day_of_week' => 1, // Monday
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            [
                'day_of_week' => 2, // Tuesday
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            [
                'day_of_week' => 3, // Wednesday
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            [
                'day_of_week' => 4, // Thursday
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            [
                'day_of_week' => 5, // Friday
                'start_time' => '09:00',
                'end_time' => '17:00',
            ],
            [
                'day_of_week' => 6, // Saturday
                'start_time' => '10:00',
                'end_time' => '15:00',
            ],
        ];

        foreach ($barbers as $barberData) {
            $barber = Barber::create($barberData);
            
            // Attach services with prices
            $services = Service::all();
            foreach ($services as $service) {
                $barber->services()->attach($service->id, [
                    'price' => $service->base_price * (1 + rand(0, 20) / 100), // Random price variation
                ]);
            }
            
            // Create working hours for each barber
            foreach ($workingHours as $workingHour) {
                WorkingHour::create([
                    'barber_id' => $barber->id,
                    ...$workingHour,
                ]);
            }
        }
    }
} 