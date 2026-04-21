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
     $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

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
    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    $barbers = Barber::pluck('id')->values(); // Misal: [1, 2, 3, 4]
    $totalBarbers = $barbers->count();

    if ($totalBarbers < 4) {
        return back()->with('error', 'Minimal harus ada 4 barber.');
    }

    $shift1 = Shift::where('name', 'Shift 1')->first();
    $shift2 = Shift::where('name', 'Shift 2')->first();

    if (!$shift1 || !$shift2) {
        return back()->with('error', 'Shift tidak lengkap.');
    }

    BarberShift::truncate();

    // Pointer utama untuk menentukan siapa yang mulai kerja hari ini
    $globalPointer = 0;

    for ($week = 1; $week <= 4; $week++) {
        foreach ($days as $day) {
            if (in_array($day, ['sabtu', 'minggu'])) {
                $needed = 4;
                $shift1Count = 3;
                $shift2Count = 3;
            } else {
                $needed = 3;
                $shift1Count = 1;
                $shift2Count = 2;
            }

            // Ambil Barber yang bertugas hari ini secara melingkar (circular)
            $workingBarberIds = [];
            for ($i = 0; $i < $needed; $i++) {
                // Modulo memastikan jika index > totalBarbers, dia balik ke 0
                $index = ($globalPointer + $i) % $totalBarbers;
                $workingBarberIds[] = $barbers[$index];
            }

            // Update pointer untuk hari berikutnya
            $globalPointer += $needed;

            // Bagi ke dalam shift
            $shift1Barbers = array_slice($workingBarberIds, 0, $shift1Count);
            $shift2Barbers = array_slice($workingBarberIds, $shift1Count, $shift2Count);

            foreach ($barbers as $barberId) {
                $currentShift = null;
                $isWorking = false;

                if (in_array($barberId, $shift1Barbers)) {
                    $currentShift = $shift1;
                    $isWorking = true;
                } elseif (in_array($barberId, $shift2Barbers)) {
                    $currentShift = $shift2;
                    $isWorking = true;
                }

                BarberShift::create([
                    'barber_id'   => $barberId,
                    'shift_id'    => $currentShift?->id,
                    'week_number' => $week,
                    'day_of_week' => $day,
                    'start_time'  => $currentShift?->start_time,
                    'end_time'    => $currentShift?->end_time,
                    'is_day_off'  => !$isWorking,
                ]);
            }
        }
    }

    return back()->with('success', 'Jadwal berhasil dibuat.');
}

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
