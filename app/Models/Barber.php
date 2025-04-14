<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'image_url',
        'working_hours'
    ];

    protected $casts = [
        'working_hours' => 'json'
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'barber_service');
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function blockedTimes(): HasMany
    {
        return $this->hasMany(BlockedTime::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getAvailableTimeSlots($date)
    {
        $dayOfWeek = date('w', strtotime($date));
        $workingHours = $this->workingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHours) {
            return [];
        }

        // Convert date string to timestamp for the start of the day
        $dateTimestamp = strtotime($date);
        
        // Get working hours timestamps for the specific date
        $startTime = strtotime($workingHours->start_time, $dateTimestamp);
        $endTime = strtotime($workingHours->end_time, $dateTimestamp);
        $interval = 30 * 60; // 30 minutes in seconds

        // Get all confirmed appointments for this date with their services
        $confirmedAppointments = $this->appointments()
            ->with('service')
            ->where('appointment_date', $date)
            ->where('status', 'confirmed')
            ->get();

        $availableSlots = [];
        $currentTime = $startTime;

        while ($currentTime < $endTime) {
            $timeSlot = date('H:i', $currentTime);
            $isBooked = false;

            foreach ($confirmedAppointments as $appointment) {
                // Convert appointment time to timestamp for the same date
                $appointmentTime = strtotime($date . ' ' . $appointment->appointment_time);
                $appointmentDuration = $appointment->service->duration_minutes * 60;
                $appointmentEndTime = $appointmentTime + $appointmentDuration;

                // Current slot's end time
                $currentSlotEndTime = $currentTime + $interval;

                // Check if there's any overlap between the current slot and the appointment
                if (
                    // Case 1: Current slot starts during an appointment
                    ($currentTime >= $appointmentTime && $currentTime < $appointmentEndTime) ||
                    // Case 2: Current slot ends during an appointment
                    ($currentSlotEndTime > $appointmentTime && $currentSlotEndTime <= $appointmentEndTime) ||
                    // Case 3: Current slot completely contains an appointment
                    ($currentTime <= $appointmentTime && $currentSlotEndTime >= $appointmentEndTime)
                ) {
                    $isBooked = true;
                    break;
                }
            }

            $availableSlots[] = [
                'time' => $timeSlot,
                'isAvailable' => !$isBooked
            ];

            $currentTime += $interval;
        }

        return $availableSlots;
    }
} 