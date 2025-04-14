<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\BarberController;
use App\Http\Controllers\API\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->group(function () {
    // Barber routes
    Route::get('/barbers', [BarberController::class, 'index']);
    Route::get('/barbers/{barber}', [BarberController::class, 'show']);
    Route::get('/barbers/{barber}/services', [BarberController::class, 'services']);
    Route::get('/barbers/{barber}/availability/{date}', [BarberController::class, 'availability']);
    Route::get('/barbers/{barber}/booked-slots', [AppointmentController::class, 'getBookedSlots']);

    // Service routes
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);

    // Appointment routes
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
    Route::post('/appointments/archive-old', [AppointmentController::class, 'archiveOld']);
    
    // For backwards compatibility
    Route::get('/bookings/available-slots/{barberId}', [AppointmentController::class, 'getAvailableSlots']);
    Route::post('/bookings', [AppointmentController::class, 'store']);

    Route::get('/appointments/available-slots/{barberId}', [AppointmentController::class, 'getAvailableSlots']);
});
