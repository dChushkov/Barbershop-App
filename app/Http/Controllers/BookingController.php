<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Barber;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function getAvailableTimeSlots($barberId, Request $request)
    {
        $barber = Barber::findOrFail($barberId);
        $date = Carbon::parse($request->date);
        
        // Get all bookings for this barber on this date
        $existingBookings = Booking::where('barber_id', $barberId)
            ->whereDate('appointment_date', $date)
            ->get()
            ->pluck('appointment_time')
            ->toArray();
        
        // Generate time slots from 9 AM to 5 PM
        $slots = [];
        $startTime = Carbon::parse($date)->setHour(9)->setMinute(0);
        $endTime = Carbon::parse($date)->setHour(17)->setMinute(0);
        
        while ($startTime < $endTime) {
            $timeStr = $startTime->format('H:i');
            $slots[] = [
                'time' => $timeStr,
                'isAvailable' => !in_array($timeStr, $existingBookings)
            ];
            $startTime->addMinutes(30);
        }
        
        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'client_name' => 'required|string',
            'client_email' => 'required|email',
            'client_phone' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $booking = Booking::create($validated);
        
        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking
        ], 201);
    }
} 