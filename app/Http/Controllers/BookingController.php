<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BarberShift;
use App\Models\BookingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    private function resolveWeekAndDay(string $date): array
    {
        $carbon = Carbon::parse($date);
        $startOfMonth = $carbon->copy()->startOfMonth();
        $weekNumber = (int) ceil(($carbon->day + $startOfMonth->dayOfWeek) / 7);
        if ($weekNumber > 4) $weekNumber = 4;

        $daysMap = [
            'Sunday'    => 'minggu',
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
        ];

        $day = $daysMap[$carbon->format('l')];

        return [$weekNumber, $day];
    }

    public function create()
    {
        return view('booking.create', [
            'services' => Service::all(),
        ]);
    }

    public function getSlotsByDate(Request $request)
{
    $request->validate(['date' => 'required|date']);

    [$weekNumber, $day] = $this->resolveWeekAndDay($request->date);

    $shifts = BarberShift::where('week_number', $weekNumber)
        ->where('day_of_week', $day)
        ->where('is_day_off', false)
        ->get();

    $slots = collect();

    foreach ($shifts as $shift) {
        $start  = Carbon::parse($shift->start_time)->minute(0);
        $end    = Carbon::parse($shift->end_time);
        $cursor = $start->copy();

        while ($cursor->lt($end)) {
            $slots->push($cursor->format('H:i'));
            $cursor->addHour();
        }
    }

    $slots = $slots->unique()->sort()->values();

    // 🔥 FILTER SLOT YANG MASIH ADA KAPSTER
    $availableSlots = $slots->filter(function ($time) use ($request, $weekNumber, $day) {

        $timeCarbon = Carbon::parse($time);

        // ambil kapster yang shift di jam ini
        $barbers = Barber::whereHas('shifts', function ($q) use ($weekNumber, $day, $timeCarbon) {
            $q->where('week_number', $weekNumber)
              ->where('day_of_week', $day)
              ->where('is_day_off', false)
              ->whereTime('start_time', '<=', $timeCarbon)
              ->whereTime('end_time', '>', $timeCarbon);
        })->get();

        // cek apakah ada yang masih kosong
        foreach ($barbers as $barber) {

            $bookings = Booking::with('services')
                ->where('barber_id', $barber->id)
                ->where('date', $request->date)
                ->get();

            $isBusy = false;

            foreach ($bookings as $b) {
                $bStart = Carbon::parse($b->time);
                $bEnd   = $bStart->copy()->addMinutes($b->services->sum('duration'));

                if ($timeCarbon->gte($bStart) && $timeCarbon->lt($bEnd)) {
                    $isBusy = true;
                    break;
                }
            }

            // 🔥 kalau ada 1 saja yang free → slot valid
            if (!$isBusy) {
                return true;
            }
        }

        // ❌ semua barber sibuk
        return false;
    });

    return response()->json($availableSlots->values());
}

    public function getAvailableBarbers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        [$weekNumber, $day] = $this->resolveWeekAndDay($request->date);

        $time = Carbon::parse($request->time);

        $barbers = Barber::whereHas('shifts', function ($q) use ($weekNumber, $day, $time) {
            $q->where('week_number', $weekNumber)
            ->where('day_of_week', $day)
            ->where('is_day_off', false)
            ->whereTime('start_time', '<=', $time)
            ->whereTime('end_time', '>', $time);
        })->with('user')->get();

        $slotStart = Carbon::parse($request->time);

        $available = $barbers->filter(function ($barber) use ($request, $slotStart) {
            $bookings = Booking::with('services')
                ->where('barber_id', $barber->id)
                ->where('date', $request->date)
                ->get();

            foreach ($bookings as $b) {
                $bStart = Carbon::parse($b->time);
                $bEnd   = $bStart->copy()->addMinutes($b->services->sum('duration'));

                if ($slotStart->gte($bStart) && $slotStart->lt($bEnd)) {
                    return false;
                }
            }

            return true;
        });

        return response()->json($available->values());
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'barber_id'   => 'required|exists:barbers,id',
            'date'        => 'required|date',
        ]);

        [$weekNumber, $day] = $this->resolveWeekAndDay($request->date);

        $shift = BarberShift::where('barber_id', $request->barber_id)
            ->where('week_number', $weekNumber)
            ->where('day_of_week', $day)
            ->where('is_day_off', false)
            ->first();

        if (!$shift) {
            return response()->json(['allSlots' => [], 'bookedSlots' => []]);
        }

        $start  = Carbon::parse($shift->start_time)->minute(0);
        $end    = Carbon::parse($shift->end_time);
        $slots  = [];
        $cursor = $start->copy();

        while ($cursor->lt($end)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addHour();
        }

        $bookedSlots = [];

        $bookings = Booking::with('services')
            ->where('barber_id', $request->barber_id)
            ->where('date', $request->date)
            ->get();

        foreach ($bookings as $b) {
            $bStart  = Carbon::parse($b->time);
            $bEnd    = $bStart->copy()->addMinutes($b->services->sum('duration'));
            $cursor  = $bStart->copy();

            while ($cursor < $bEnd) {
                $bookedSlots[] = $cursor->format('H:i');
                $cursor->addHour();
            }
        }

        $bookedSlots = collect($bookedSlots)->unique()->values();

        return response()->json(['allSlots' => $slots, 'bookedSlots' => $bookedSlots]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barber_id'    => 'required|exists:barbers,id',
            'service_ids'  => 'required|array|min:1',
            'service_ids.*'=> 'exists:services,id',
            'date'         => 'required|date',
            'time'         => 'required',
        ]);

        $barber   = Barber::findOrFail($request->barber_id);
        $services = Service::whereIn('id', $request->service_ids)->get();

        $totalDuration = 0;
        $totalPrice    = 0;
        $hasHaircut    = false;

        foreach ($services as $service) {
            $totalDuration += $service->duration;

            if (strtolower($service->name) === 'haircut') {
                $totalPrice += $barber->price;
                $hasHaircut  = true;
            } else {
                $totalPrice += $service->price;
            }
        }

        $startTime = Carbon::parse($request->time);
        $endTime   = $startTime->copy()->addMinutes($totalDuration);

        $existing = Booking::with('services')
            ->where('barber_id', $barber->id)
            ->where('date', $request->date)
            ->get();

        foreach ($existing as $b) {
            $bStart = Carbon::parse($b->time);
            $bEnd   = $bStart->copy()->addMinutes($b->services->sum('duration'));

            if ($startTime < $bEnd && $endTime > $bStart) {
                return back()->with('error', 'Waktu bentrok dengan booking lain');
            }
        }

        $adminFee = 5000;

        $booking = Booking::create([
            'booking_code' => 'BOOK-' . strtoupper(uniqid()),
            'user_id'      => auth()->id(),
            'customer_name' => auth()->user()->name,
            'source' => 'online',
            'barber_id'    => $barber->id,
            'date'         => $request->date,
            'time'         => $startTime,
            'barber_price' => $hasHaircut ? $barber->price : 0,
            'service_price'=> $totalPrice,
            'total_price'  => $totalPrice + $adminFee,
            'status'       => 'pending',
        ]);

        foreach ($services as $service) {
            BookingService::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'price'      => strtolower($service->name) === 'haircut' ? $barber->price : $service->price,
                'duration'   => $service->duration,
            ]);
        }

        return redirect()->route('booking.history')->with('success', 'Booking berhasil dibuat');
    }

  public function history(Request $request)
{
    $search = $request->query('search');
    $status = $request->query('status');

    $bookings = Booking::with(['barber.user', 'services.service'])
        ->where('user_id', auth()->id())
        ->when($search, function ($q) use ($search) {
            $q->whereHas('services.service', function ($qs) use ($search) {
                $qs->where('name', 'like', "%{$search}%");
            })->orWhereHas('barber.user', function ($qb) use ($search) {
                $qb->where('name', 'like', "%{$search}%");
            });
        })
        ->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->orderByRaw("
            CASE status
                WHEN 'pending'   THEN 1
                WHEN 'confirmed' THEN 2
                WHEN 'completed' THEN 3
                WHEN 'canceled'  THEN 4
                ELSE 5
            END
        ")
        ->orderBy('date', 'desc')
        ->orderBy('time', 'desc')
        ->paginate(3)
        ->withQueryString();

    return view('booking.history', compact('bookings', 'search', 'status'));
}

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan');
        }

        $booking->update(['status' => 'canceled']);

        return back()->with('success', 'Booking berhasil dibatalkan');
    }
}