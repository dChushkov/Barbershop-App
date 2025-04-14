<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }
} 