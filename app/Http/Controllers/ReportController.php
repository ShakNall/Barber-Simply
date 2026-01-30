<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Barber;
use App\Models\BookingService;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? Carbon::today()->toDateString();
        $to = $request->to ?? Carbon::today()->toDateString();

        $bookings = Booking::with(['barber.user', 'service', 'user'])
            ->whereBetween('date', [$from, $to])
            ->where('payment_status', 'paid')
            ->orderBy('date', 'asc')
            ->get();

        $totalIncome = $bookings->sum('total_price');

        $paymentBreakdown = [
            'cash' => $bookings->where('payment_method', 'cash')->sum('total_price'),
            'qris' => $bookings->where('payment_method', 'qris')->sum('total_price'),
        ];

        $incomePerBarber = Barber::with('user')
            ->withSum(['bookings as income' => function ($q) use ($from, $to) {
                $q->where('payment_status', 'paid')
                    ->whereBetween('date', [$from, $to]);
            }], 'total_price')
            ->get()
            ->map(function ($barber) {
                return [
                    'name' => $barber->user->name,
                    'income' => $barber->income ?? 0
                ];
            });


        $incomePerService = BookingService::with('service')
            ->whereHas('booking', function ($q) use ($from, $to) {
                $q->where('payment_status', 'paid')
                    ->whereBetween('date', [$from, $to]);
            })
            ->get()
            ->groupBy('service_id')
            ->map(function ($rows) {
                return [
                    'name'   => $rows->first()->service->name,
                    'income' => $rows->sum('price'),
                ];
            })
            ->values();

        return view('reports.index', compact(
            'from',
            'to',
            'bookings',
            'totalIncome',
            'paymentBreakdown',
            'incomePerBarber',
            'incomePerService'
        ));
    }

    public function barberReport(Request $request, $id)
    {
        $from = $request->from ?? now()->toDateString();
        $to   = $request->to ?? now()->toDateString();

        $barber = Barber::with('user')->findOrFail($id);

        $bookings = Booking::with(['service', 'user'])
            ->where('barber_id', $id)
            ->whereBetween('date', [$from, $to])
            ->where('payment_status', 'paid')
            ->orderBy('date', 'asc')
            ->get();

        $totalIncome = $bookings->sum('total_price');
        $totalBooking = $bookings->count();
        $average = $totalBooking > 0 ? $totalIncome / $totalBooking : 0;

        $topServices = $bookings->groupBy('service_id')->map(function ($row) {
            return [
                'service' => $row->first()->service->name,
                'count' => $row->count(),
                'income' => $row->sum('total_price')
            ];
        })->sortByDesc('count')->values();

        return view('reports.barber', compact(
            'barber',
            'from',
            'to',
            'totalIncome',
            'totalBooking',
            'average',
            'bookings',
            'topServices'
        ));
    }
}
