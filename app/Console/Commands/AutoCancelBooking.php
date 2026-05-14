<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCancelBooking extends Command
{
    // Nama command
    protected $signature = 'booking:auto-cancel';

    // Deskripsi
    protected $description = 'Batalkan booking pending & confirmed jika sudah melewati hari H';

    public function handle()
    {
        // 1. Ambil tanggal hari ini (jam 00:00:00)
        $today = Carbon::today();

        $expiredBookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->where('date', '<', $today)
            ->get();

        $count = $expiredBookings->count();

        if ($count > 0) {
            foreach ($expiredBookings as $booking) {
                $booking->update(['status' => 'canceled']);
            }
            $this->info("Berhasil membatalkan {$count} booking (Pending/Confirmed) yang sudah lewat hari.");
        } else {
            $this->info("Tidak ada booking yang kedaluwarsa hari ini.");
        }
    }
}