<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\BarberShift;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $barbers = Barber::with(['user', 'shifts.shift'])->get();
        $shifts = Shift::all();

        return view('shifts.index', compact('barbers', 'days', 'shifts'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'barber_id' => 'required',
            'week_number' => 'required',
            'day_of_week' => 'required',
            'shift_id' => 'nullable|exists:shifts,id',
            'is_day_off' => 'required|boolean'
        ]);

        $shift = $request->shift_id
            ? Shift::find($request->shift_id)
            : null;

        BarberShift::updateOrCreate(
            [
                'barber_id' => $request->barber_id,
                'week_number' => $request->week_number,
                'day_of_week' => $request->day_of_week,
            ],
            [
                'shift_id' => $request->is_day_off ? null : $shift?->id,
                'start_time' => $request->is_day_off ? null : $shift?->start_time,
                'end_time' => $request->is_day_off ? null : $shift?->end_time,
                'is_day_off' => $request->is_day_off,
            ]
        );


        return back()->with('success', 'Shift berhasil disimpan.');
    }

    public function rolling()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $barbers = Barber::pluck('id')->values();
        $totalBarbers = $barbers->count();

        if ($totalBarbers < 2) {
            return back()->with('success', 'Minimal harus ada 2 barber.');
        }

        // reset jadwal lama
        BarberShift::truncate();

        $startTime = '09:00';
        $endTime   = '17:00';

        /**
         * Generate 4 minggu
         */
        for ($week = 1; $week <= 4; $week++) {

            /**
             * Geser urutan barber tiap minggu
             * supaya hari libur tidak selalu jatuh ke orang yang sama
             */
            $rotatedBarbers = $barbers->slice($week - 1)
                ->merge($barbers->slice(0, $week - 1))
                ->values();

            foreach ($days as $dayIndex => $day) {

                /**
                 * Tentukan barber libur hari ini
                 */
                $offBarberId = $rotatedBarbers[$dayIndex % $totalBarbers];

                foreach ($barbers as $barberId) {

                    $isWorking = $barberId !== $offBarberId;

                    BarberShift::create([
                        'barber_id'   => $barberId,
                        'week_number' => $week,
                        'day_of_week' => $day,
                        'start_time'  => $isWorking ? $startTime : null,
                        'end_time'    => $isWorking ? $endTime : null,
                        'is_day_off'  => !$isWorking,
                    ]);
                }
            }
        }

        return back()->with('success', 'Jadwal rolling 4 minggu berhasil dibuat.');
    }

    /* ================= MASTER SHIFT ================= */

    public function storeMaster(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        Shift::create($data);

        return back()->with('success', 'Master shift ditambahkan');
    }

    public function updateMaster(Request $request, Shift $shift)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        $shift->update($data);

        return back()->with('success', 'Master shift diperbarui');
    }

    public function destroyMaster(Shift $shift)
    {
        if ($shift->barberShifts()->exists()) {
            return back()->with('error', 'Shift sedang dipakai jadwal barber');
        }

        $shift->delete();

        return back()->with('success', 'Master shift dihapus');
    }
}
