<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::with('services')->get();
        
        return response()->json([
            'data' => $barbers->map(function ($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->name,
                    'bio' => $barber->bio,
                    'photo' => $barber->photo,
                    'services' => $barber->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                        ];
                    }),
                ];
            }),
        ]);
    }
} 