<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'barber_id',
        'date',
        'time',
        'customer_name',
        'source',
        'status',
        'service_price',
        'barber_price',
        'total_price',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getCalculatedTotalAttribute()
    {
        return $this->service_price + $this->barber_price;
    }

    public function getCustomerLabelAttribute()
    {
        return $this->source === 'walk_in'
            ? $this->customer_name
            : optional($this->user)->name;
    }

    public function services()
    {
        return $this->hasMany(BookingService::class);
    }

    public function getTotalServiceDurationAttribute()
    {
        return $this->services->sum('duration');
    }

    public function getTotalServicePriceAttribute()
    {
        return $this->services->sum('price');
    }
}
