<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_day_off',
        'week_number',
        'shift_id',
    ];

    protected $casts = [
        'is_day_off' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
