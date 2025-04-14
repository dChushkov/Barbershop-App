<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// API routes
Route::prefix('api')->group(function () {
    Route::get('/barbers', [BarberController::class, 'index']);
});

// SPA entry point - this should be the first route
Route::get('/', function () {
    return view('app');
});

// Catch all other routes and redirect to the SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
