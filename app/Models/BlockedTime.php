<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
} 