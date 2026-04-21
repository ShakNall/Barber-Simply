<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BarberShift;
use App\Models\BookingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{

    private function resolveWeekAndDay(string $date): array
    {
        $carbon = Carbon::parse($date);

        $weekOfMonth = $carbon->weekOfMonth;     // 1–5
        $weekNumber = (($weekOfMonth - 1) % 4) + 1; // normalize ke 1–4
        $day = strtolower($carbon->format('l')); // monday, dst

        return [$weekNumber, $day];
    }
    public function create()
    {
        return view('booking.create', [
            'barbers' => collect(),
            'services' => Service::all(),
        ]);
    }

    /* =============================
        BARBER TERSEDIA BY TANGGAL
    ============================== */
   public function getAvailableBarbers(Request $request)
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $date = \Carbon\Carbon::parse($request->date);
    
    $daysMap = [
        'Sunday' => 'minggu',
        'Monday' => 'senin',
        'Tuesday' => 'selasa',
        'Wednesday' => 'rabu',
        'Thursday' => 'kamis',
        'Friday' => 'jumat',
        'Saturday' => 'sabtu',
    ];

    $day = $daysMap[$date->format('l')];
    
    $startOfMonth = $date->copy()->startOfMonth();
    $weekNumber = (int) ceil(($date->day + $startOfMonth->dayOfWeek) / 7);
    
    if ($weekNumber > 4) {
        $weekNumber = 4;
    }

    $barbers = Barber::whereHas('shifts', function ($q) use ($weekNumber, $day) {
        $q->where('week_number', $weekNumber)
            ->where('day_of_week', $day)
            ->where('is_day_off', false);
    })->with('user')->get();

    return response()->json($barbers);
}

    /* =============================
        SLOT JAM
    ============================== */
  public function getAvailableSlots(Request $request)
{
    $request->validate([
        'barber_id' => 'required|exists:barbers,id',
        'date' => 'required|date',
    ]);

    $date = \Carbon\Carbon::parse($request->date);
    
    $daysMap = [
        'Sunday' => 'minggu',
        'Monday' => 'senin',
        'Tuesday' => 'selasa',
        'Wednesday' => 'rabu',
        'Thursday' => 'kamis',
        'Friday' => 'jumat',
        'Saturday' => 'sabtu',
    ];

    $day = $daysMap[$date->format('l')];
    
    $startOfMonth = $date->copy()->startOfMonth();
    $weekNumber = (int) ceil(($date->day + $startOfMonth->dayOfWeek) / 7);
    
    if ($weekNumber > 4) {
        $weekNumber = 4;
    }

    $shift = BarberShift::where('barber_id', $request->barber_id)
        ->where('week_number', $weekNumber)
        ->where('day_of_week', $day)
        ->where('is_day_off', false)
        ->first();

    if (!$shift) {
        return response()->json([
            'allSlots' => [],
            'bookedSlots' => [],
        ]);
    }

    $start = \Carbon\Carbon::parse($shift->start_time)->minute(0);
    $end = \Carbon\Carbon::parse($shift->end_time);

    $slots = [];
    $cursor = $start->copy();

    while ($cursor->lt($end)) {
        $slots[] = $cursor->format('H:i');
        $cursor->addHour();
    }

    $booked = \App\Models\Booking::where('barber_id', $request->barber_id)
        ->where('date', $request->date)
        ->pluck('time')
        ->map(function ($time) {
            return \Carbon\Carbon::parse($time)->minute(0)->format('H:i');
        })
        ->unique()
        ->values();

    return response()->json([
        'allSlots' => $slots,
        'bookedSlots' => $booked,
    ]);
}
    public function store(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $barber = Barber::findOrFail($request->barber_id);
        $services = Service::whereIn('id', $request->service_ids)->get();

        $totalDuration = 0;
        $totalPrice = 0;
        $hasHaircut = false;

        foreach ($services as $service) {
            $totalDuration += $service->duration;

            if (strtolower($service->name) === 'haircut') {
                $totalPrice += $barber->price;
                $hasHaircut = true;
            } else {
                $totalPrice += $service->price;
            }
        }

        $startTime = Carbon::parse($request->time);
        $endTime = $startTime->copy()->addMinutes($totalDuration);

        $existing = Booking::with('services')
            ->where('barber_id', $barber->id)
            ->where('date', $request->date)
            ->get();

        foreach ($existing as $b) {
            $bStart = Carbon::parse($b->time);
            $bEnd = $bStart->copy()->addMinutes(
                $b->services->sum('duration')
            );

            if ($startTime < $bEnd && $endTime > $bStart) {
                return back()->with('error', 'Waktu bentrok dengan booking lain');
            }
        }

        $adminFee = 5000;

        $booking = Booking::create([
            'booking_code' => 'BOOK-' . strtoupper(uniqid()),
            'user_id' => auth()->id(),
            'barber_id' => $barber->id,
            'date' => $request->date,
            'time' => $startTime,
            'barber_price' => $hasHaircut ? $barber->price : 0,
            'service_price' => $totalPrice,
            'total_price' => $totalPrice + $adminFee,
            'status' => 'pending',
        ]);

        foreach ($services as $service) {
            BookingService::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'price' => strtolower($service->name) === 'haircut'
                    ? $barber->price
                    : $service->price,
                'duration' => $service->duration,
            ]);
        }

        return redirect()->route('booking.history')
            ->with('success', 'Booking berhasil dibuat');
    }


    public function history(Request $request)
    {
        $search = $request->query('search');

        $bookings = Booking::with(['barber.user', 'services.service'])
            ->where('user_id', auth()->id())
            ->when($search, function ($q) use ($search) {
                $q->whereHas('services.service', function ($qs) use ($search) {
                    $qs->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('barber.user', function ($qb) use ($search) {
                        $qb->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'confirmed' THEN 2
                    WHEN 'completed' THEN 3
                    WHEN 'canceled' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(3)  // jumlah card per halaman
            ->withQueryString(); // biar search kebawa saat pindah page

        return view('booking.history', compact('bookings', 'search'));
    }


    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan');
        }

        $booking->update([
            'status' => 'canceled',
        ]);

        return back()->with('success', 'Booking berhasil dibatalkan');
    }
}
