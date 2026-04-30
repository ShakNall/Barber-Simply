<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCancelBooking extends Command
{
    // Nama command yang akan dipanggil di terminal
    protected $signature = 'booking:auto-cancel';

    // Deskripsi singkat
    protected $description = 'Batalkan booking yang statusnya pending lebih dari 24 jam';

    public function handle()
    {
        // Cari booking dengan status pending yang dibuat lebih dari 24 jam yang lalu
        $expiredBookings = Booking::where('status', 'pending')
            ->where('date', '<', Carbon::now()->subDay())
            ->get();

        $count = $expiredBookings->count();

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'canceled']);
        }

        $this->info("Berhasil membatalkan {$count} booking yang kedaluwarsa.");
    }
}