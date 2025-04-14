<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Get available time slots for a barber on a specific date
     */
    public function getAvailableSlots($barberId, Request $request)
    {
        try {
            $date = $request->query('date');
            if (!$date) {
                return response()->json(['message' => 'Date is required'], 400);
            }

            // Set timezone to Bulgarian time
            Carbon::setLocale('bg');
            $requestDate = Carbon::parse($date)->setTimezone('Europe/Sofia')->startOfDay();
            $now = Carbon::now('Europe/Sofia');

            // Generate all possible time slots for the day (9:00 - 18:00)
            $timeSlots = [];
            $startTime = Carbon::parse($date . ' 09:00:00', 'Europe/Sofia');
            $endTime = Carbon::parse($date . ' 18:00:00', 'Europe/Sofia');

            while ($startTime < $endTime) {
                // If it's today, skip past times
                if ($requestDate->isToday() && $startTime->lt($now)) {
                    $startTime->addMinutes(30);
                    continue;
                }

                $timeSlots[] = [
                    'time' => $startTime->format('H:i'),
                    'isAvailable' => true
                ];
                $startTime->addMinutes(30);
            }

            // Get booked appointments for this date and barber
            $bookedAppointments = Appointment::where('barber_id', $barberId)
                ->whereDate('appointment_date', $requestDate)
                ->where('status', '!=', 'cancelled')
                ->get();

            // Mark booked slots as unavailable based on service duration
            foreach ($bookedAppointments as $appointment) {
                $appointmentTime = Carbon::parse($appointment->appointment_time, 'Europe/Sofia');
                
                // Get service duration
                $service = Service::find($appointment->service_id);
                $duration = $service ? $service->duration_minutes : 30;
                $slotsToBlock = ceil($duration / 30);
                
                // Block all slots needed for this service
                for ($i = 0; $i < $slotsToBlock; $i++) {
                    $blockTime = $appointmentTime->copy()->addMinutes(30 * $i)->format('H:i');
                    
                    foreach ($timeSlots as &$slot) {
                        if ($slot['time'] === $blockTime) {
                            $slot['isAvailable'] = false;
                            break;
                        }
                    }
                }
            }

            // Return with slots property to match frontend expectations
            return response()->json([
                'slots' => $timeSlots
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available slots: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get available slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all appointments for a specific barber and date
     */
    public function index(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('Appointment request received', [
                'barber_id' => $request->input('barber_id'),
                'from_date' => $request->input('from_date')
            ]);

            // Direct database query to get appointments
            $query = DB::table('appointments')
                ->select('appointments.*')
                ->when($request->has('barber_id'), function($query) use ($request) {
                    return $query->where('barber_id', $request->input('barber_id'));
                })
                ->when($request->has('from_date'), function($query) use ($request) {
                    return $query->where('appointment_date', '>=', $request->input('from_date'));
                })
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc');

            $appointments = $query->get();
            
            // Log what we found
            Log::info('Found appointments', [
                'count' => count($appointments),
                'appointments' => $appointments
            ]);

            return response()->json([
                'data' => $appointments
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching appointments', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch appointments',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'barber_id' => 'required|exists:barbers,id',
                'service_id' => 'required|exists:services,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'client_name' => 'required|string',
                'client_email' => 'required|email',
                'client_phone' => 'required|string',
                'status' => 'required|in:confirmed,cancelled,completed'
            ]);

            // Get service duration
            $service = Service::find($data['service_id']);
            if (!$service) {
                throw new \Exception('Service not found');
            }

            // Check if all required slots are available
            $appointmentDateTime = Carbon::parse($data['appointment_date'] . ' ' . $data['appointment_time'], 'Europe/Sofia');
            $slotsToCheck = ceil($service->duration_minutes / 30);
            
            for ($i = 0; $i < $slotsToCheck; $i++) {
                $checkTime = $appointmentDateTime->copy()->addMinutes(30 * $i);
                
                $existingAppointment = Appointment::where('barber_id', $data['barber_id'])
                    ->whereDate('appointment_date', $checkTime->toDateString())
                    ->whereTime('appointment_time', $checkTime->format('H:i'))
                    ->where('status', '!=', 'cancelled')
                    ->first();
                
                if ($existingAppointment) {
                    throw new \Exception('Selected time slot is not available');
                }
            }

            // Convert time to UTC before storing
            $data['appointment_time'] = $appointmentDateTime->format('H:i');

            $appointment = Appointment::create($data);

            return response()->json([
                'message' => 'Appointment created successfully',
                'appointment' => $appointment
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating appointment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Appointment $booking)
    {
        return response()->json($booking);
    }

    /**
     * Cancel a booking
     */
    public function cancel($id)
    {
        try {
            $affected = DB::table('appointments')
                ->where('id', $id)
                ->update(['status' => 'cancelled']);

            if ($affected === 0) {
                return response()->json(['message' => 'Booking not found'], 404);
            }

            return response()->json(['message' => 'Booking cancelled']);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to cancel booking'], 500);
        }
    }

    /**
     * Archive old appointments (soft delete)
     */
    public function archiveOld(Request $request)
    {
        try {
            $date = $request->input('before_date');
            if (!$date) {
                $date = now()->subMonth()->format('Y-m-d'); // Default to 1 month ago
            }

            $count = Appointment::where('appointment_date', '<', $date)->delete();

            return response()->json([
                'message' => "Successfully archived $count appointments",
                'archived_before_date' => $date
            ]);

        } catch (\Exception $e) {
            Log::error('Error archiving old appointments: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to archive old appointments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 