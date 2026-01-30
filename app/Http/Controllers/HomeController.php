<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ======================= CUSTOMER DASHBOARD =======================
        if ($user->role === 'customer') {
            $today = Carbon::today();

            $myBookings = Booking::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get();

            $todayQueues = Booking::whereDate('date', $today)
                ->whereNotIn('status', ['completed', 'canceled'])
                ->orderBy('time', 'asc')
                ->get();

            $upcoming = Booking::where('user_id', $user->id)
                ->where('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->first();

            return view('home', [
                'mode' => 'customer',
                'myBookings' => $myBookings,
                'upcoming' => $upcoming,
                'todayQueues' => $todayQueues,
            ]);
        }

        // ======================= ADMIN DASHBOARD =======================
        $today = Carbon::today();

        $totalBookingsToday = Booking::whereDate('date', $today)->count();
        $incomeToday = Booking::whereDate('date', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');
        $newCustomersToday = User::where('role', 'customer')
            ->whereDate('created_at', $today)
            ->count();

        $statusCounts = [
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checkin'   => Booking::where('status', 'checkin')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
        ];

        $last7days = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i)->toDateString();
            return [
                'date'  => $date,
                'count' => Booking::whereDate('date', $date)->count(),
            ];
        });

        $last30days = collect(range(29, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i)->toDateString();
            return [
                'date'   => $date,
                'income' => Booking::whereDate('date', $date)
                    ->where('payment_status', 'paid')
                    ->sum('total_price'),
            ];
        });

        $recentBookings = Booking::with(['user', 'barber.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $barberTop = \App\Models\Barber::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        return view('home', [
            'mode' => 'admin',
            'totalBookingsToday' => $totalBookingsToday,
            'incomeToday' => $incomeToday,
            'newCustomersToday' => $newCustomersToday,
            'statusCounts' => $statusCounts,
            'last7days' => $last7days,
            'last30days' => $last30days,
            'recentBookings' => $recentBookings,
            'barberTop' => $barberTop,
        ]);
    }
}
