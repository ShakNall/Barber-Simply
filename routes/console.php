<?php

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \Log::info('Cron job jalan!'); // Tambahkan log ini buat bukti
    $expiredCount = \App\Models\Booking::where('status', 'pending')
        ->where('date', '<', now()->subDay())
        ->update(['status' => 'canceled']);
    
    \Log::info("Berhasil membatalkan: " . $expiredCount);
})->everyMinute(); // GANTI INI SEMENTARA