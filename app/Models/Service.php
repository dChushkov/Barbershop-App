<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
        'image_url'
    ];

    protected $casts = [
        'duration' => 'integer',
        'price' => 'decimal:2'
    ];

    public function barbers(): BelongsToMany
    {
        return $this->belongsToMany(Barber::class, 'barber_service');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
} 