<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BarberBookingController extends Controller
{
    public function index()
    {
        $barberId = auth()->user()->barber->id;

        $bookings = Booking::with(['user', 'service'])
            ->where('barber_id', $barberId)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('barber.bookings.index', compact('bookings'));
    }
    public function shifts()
    {
        $barber = auth()->user()->barber;

        $shifts = $barber->shifts()
            ->orderBy('week_number')
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('week_number');

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        return view('barber.shifts.index', compact('shifts', 'days'));
    }
}
