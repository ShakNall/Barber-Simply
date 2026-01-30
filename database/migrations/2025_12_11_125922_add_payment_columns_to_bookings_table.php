<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            // Tambahkan jika belum ada
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'qris', 'transfer'])
                    ->nullable()
                    ->after('status');
            }

            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid'])
                    ->default('unpaid')
                    ->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {});
    }
};
