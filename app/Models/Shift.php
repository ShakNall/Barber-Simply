<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time'];

    public function barberShifts()
    {
        return $this->hasMany(BarberShift::class);
    }
}
