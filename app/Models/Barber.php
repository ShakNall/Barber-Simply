<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'speciality',
        'is_active',
        'price',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // User owner (akun barber)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Shifts (jadwal kerja)
    public function shifts()
    {
        return $this->hasMany(BarberShift::class);
    }

    // Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Reviews diterima oleh barber
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
