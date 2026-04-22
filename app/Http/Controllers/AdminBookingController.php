<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $today = now()->format('Y-m-d');

        $query = Booking::with(['barber.user', 'services.service', 'user'])
            ->when($user->role === 'barber', function ($q) use ($user) {
                $q->where('barber_id', optional($user->barber)->id ?? 0);
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->barber, fn($q) => $q->where('barber_id', $request->barber))
            ->when($request->from && $request->to, fn($q) => $q->whereBetween('date', [$request->from, $request->to]))
            ->orderByRaw("CASE WHEN status IN ('completed', 'canceled') THEN 1 ELSE 0 END")
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc');

        $allBookings = $query->get();

        $bookingsOnline = $allBookings->where('source', 'online');
        $bookingsWalkin = $allBookings->where('source', 'walk_in');

        $antrianHariIni = $query->whereDate('date', $today)
            ->whereNotIn('status', ['completed', 'canceled'])
            ->orderByRaw("CASE WHEN source = 'online' THEN 0 ELSE 1 END")
            ->orderBy('time', 'asc')
            ->get();

        $date = now();
    
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

        $barbers = ($user->role === 'barber')
            ? Barber::where('id', optional($user->barber)->id)->with('user')->get()
            : Barber::whereHas('shifts', function ($q) use ($weekNumber, $day) {
            $q->where('week_number', $weekNumber)
                ->where('day_of_week', $day)
                ->where('is_day_off', false);
        })->with('user')->get();

        $services = Service::all();

        return view('admin.bookings.index', compact(
            'bookingsOnline',
            'bookingsWalkin',
            'antrianHariIni',
            'barbers',
            'services'
        ));
    }

    public function updateStatus(Request $request)
    {
        $booking = Booking::findOrFail($request->id);
        $booking->status = $request->status;
        $booking->save();

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'payment_method' => 'required|in:cash,qris,transfer',
        ]);

        $booking = Booking::findOrFail($request->id);
        $booking->status = 'completed';
        $booking->payment_method = $request->payment_method;
        $booking->payment_status = 'paid';
        $booking->save();

        return back()->with('success', 'Booking berhasil diselesaikan.');
    }

    public function walkIn(Request $request)
{
    $request->validate([
        'customer_name' => 'required',
        'barber_id'     => 'required|exists:barbers,id',
        'service_ids'   => 'required|array|min:1',
        'service_ids.*' => 'exists:services,id',
    ]);

    $barber = Barber::findOrFail($request->barber_id);

    $totalServicePrice = 0;
    $totalDuration     = 0;
    $hasHaircut        = false;

    foreach ($request->service_ids as $serviceId) {
        $service = Service::findOrFail($serviceId);

        if (strtolower($service->name) === 'haircut') {
            $hasHaircut = true;
        }

        $totalServicePrice += $service->price;
        $totalDuration     += $service->duration;
    }

    $barberPrice = $hasHaircut ? $barber->price : 0;
    $totalPrice  = $totalServicePrice + $barberPrice;

    $booking = Booking::create([
        'booking_code'  => 'WI-' . now()->format('YmdHis'),
        'user_id'       => null,
        'customer_name' => $request->customer_name,
        'source'        => 'walk_in',

        'barber_id' => $barber->id,
        'date'      => now()->format('Y-m-d'),
        'time'      => now()->format('H:i:s'),

        'service_price' => $totalServicePrice,
        'barber_price'  => $barberPrice,
        'total_price'   => $totalPrice,

        'status'         => 'checkin',
        'payment_status' => 'unpaid',
    ]);

    foreach ($request->service_ids as $serviceId) {
        $service = Service::findOrFail($serviceId);

        $booking->services()->create([
            'service_id' => $service->id,
            'price'      => $service->price,
            'duration'   => $service->duration,
        ]);
    }

    return back()->with('success', 'Order walk-in berhasil dibuat (CHECK-IN).');
}


    public function updateServices(Request $request)
    {
        $request->validate([
            'booking_id'    => 'required|exists:bookings,id',
            'service_ids'   => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
        ]);

        $booking = Booking::with(['services.service', 'barber'])
            ->findOrFail($request->booking_id);

        if ($booking->status === 'completed') {
            return back()->with('error', 'Booking sudah selesai, tidak bisa diubah.');
        }

        $booking->services()->delete();

        $totalServicePrice = 0;
        $totalDuration     = 0;
        $hasHaircut        = false;

        foreach ($request->service_ids as $serviceId) {
            $service = Service::findOrFail($serviceId);

            if (strtolower($service->name) === 'haircut') {
                $hasHaircut = true;
            }

            $booking->services()->create([
                'service_id' => $service->id,
                'price'      => $service->price,
                'duration'   => $service->duration,
            ]);

            $totalServicePrice += $service->price;
            $totalDuration     += $service->duration;
        }

        $barberPrice = $hasHaircut ? $booking->barber->price : 0;

        $totalPrice = $totalServicePrice + $barberPrice;

        $adminFee = $booking->source === 'online' ? 5000 : 0;
        $totalPrice += $adminFee;

        $booking->update([
            'service_price' => $totalServicePrice,
            'barber_price'  => $barberPrice,
            'total_price'   => $totalPrice,
        ]);

        return back()->with('success', 'Service booking berhasil diperbarui dan harga disesuaikan.');
    }


    public function changeBarber(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'barber_id'  => 'required|exists:barbers,id',
        ]);

        $booking = Booking::with(['services.service'])->findOrFail($request->booking_id);

        if (in_array($booking->status, ['completed', 'canceled'])) {
            return back()->with('error', 'Booking tidak bisa diubah');
        }

        $newBarber = Barber::findOrFail($request->barber_id);

        $hasHaircut = $booking->services->contains(function ($svc) {
            return strtolower($svc->service->name) === 'haircut';
        });

        $servicePrice = $booking->service_price;
        $barberPrice  = $hasHaircut ? $newBarber->price : 0;

        $booking->update([
            'barber_id'    => $newBarber->id,
            'barber_price' => $barberPrice,
            'total_price'  => $servicePrice + $barberPrice,
        ]);

        return back()->with('success', 'Kapster berhasil diganti dan harga diperbarui');
    }
}
