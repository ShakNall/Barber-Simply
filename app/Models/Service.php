<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price',
        'duration',
        'description'
    ];

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }
}
