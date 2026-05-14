<?php

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::call(function () {
    Log::info('Cron job auto-cancel dijalankan pada: ' . now());

    $expiredCount = Booking::whereIn('status', ['pending', 'confirmed'])
        ->where('date', '<', Carbon::today()) 
        ->update(['status' => 'canceled']);
    
    if ($expiredCount > 0) {
        Log::info("Auto-cancel berhasil: {$expiredCount} booking telah dibatalkan.");
    }
})->everyMinute();