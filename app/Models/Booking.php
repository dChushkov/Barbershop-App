<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'barber_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'client_name',
        'client_email',
        'client_phone',
        'notes',
        'status'
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
} 