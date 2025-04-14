<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\JsonResponse;

class BarberController extends Controller
{
    public function index(): JsonResponse
    {
        $barbers = Barber::with(['services', 'workingHours'])
            ->get()
            ->map(function ($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'photo' => $barber->photo,
                    'bio' => $barber->bio,
                    'services' => $barber->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->pivot->price,
                            'duration_minutes' => $service->duration_minutes,
                        ];
                    }),
                    'working_hours' => $barber->workingHours->map(function ($hours) {
                        return [
                            'day_of_week' => $hours->day_of_week,
                            'start_time' => $hours->start_time,
                            'end_time' => $hours->end_time,
                        ];
                    }),
                ];
            });

        return response()->json([
            'data' => $barbers,
        ]);
    }

    public function show(Barber $barber): JsonResponse
    {
        $barber->load(['services', 'workingHours']);

        return response()->json([
            'data' => [
                'id' => $barber->id,
                'name' => $barber->name,
                'photo' => $barber->photo,
                'bio' => $barber->bio,
                'services' => $barber->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->pivot->price,
                        'duration_minutes' => $service->duration_minutes,
                    ];
                }),
                'working_hours' => $barber->workingHours->map(function ($hours) {
                    return [
                        'day_of_week' => $hours->day_of_week,
                        'start_time' => $hours->start_time,
                        'end_time' => $hours->end_time,
                    ];
                }),
            ],
        ]);
    }

    public function availability(Barber $barber, string $date): JsonResponse
    {
        // Load barber's working hours and appointments for the given date
        $barber->load(['workingHours', 'appointments' => function ($query) use ($date) {
            $query->whereDate('appointment_date', $date);
        }]);

        // Get working hours for the given date's day of week
        $dayOfWeek = date('w', strtotime($date));
        $workingHours = $barber->workingHours->where('day_of_week', $dayOfWeek)->first();

        if (!$workingHours) {
            return response()->json([
                'message' => 'Barber is not working on this day',
                'available_slots' => [],
            ]);
        }

        // Generate time slots
        $slots = [];
        $currentTime = strtotime($workingHours->start_time);
        $endTime = strtotime($workingHours->end_time);

        while ($currentTime < $endTime) {
            $timeSlot = date('H:i', $currentTime);
            
            // Check if slot is available (not booked)
            $isBooked = $barber->appointments->contains(function ($appointment) use ($timeSlot) {
                return $appointment->appointment_time === $timeSlot;
            });

            if (!$isBooked) {
                $slots[] = $timeSlot;
            }

            // Add 30 minutes for next slot
            $currentTime = strtotime('+30 minutes', $currentTime);
        }

        return response()->json([
            'data' => [
                'date' => $date,
                'working_hours' => [
                    'start_time' => $workingHours->start_time,
                    'end_time' => $workingHours->end_time,
                ],
                'available_slots' => $slots,
            ],
        ]);
    }
} 