<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Barber;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Service::all();
    }

    public function getBarberServices($barberId)
    {
        $barber = Barber::findOrFail($barberId);
        return $barber->services;
    }

    public function show($id)
    {
        return Service::findOrFail($id);
    }
} 