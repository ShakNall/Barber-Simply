<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('users')) {
            Schema::dropIfExists('users');
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', ['customer', 'barber', 'admin', 'owner'])
                ->default('customer');
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | BARBERS
        |--------------------------------------------------------------------------
        */
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('nickname')->nullable();
            $table->string('speciality')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('price')->default(0);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | SERVICES
        |--------------------------------------------------------------------------
        */
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('price')->default(0);      // Harga dasar service
            $table->integer('duration')->default(30);  // Menit
            $table->string('description')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | BARBER SHIFTS (jadwal kerja per hari)
        |--------------------------------------------------------------------------
        */
        Schema::create('barber_shifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barber_id')->constrained('barbers')->cascadeOnDelete();

            $table->enum('day_of_week', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->tinyInteger('week_number');
            $table->boolean('is_day_off')->default(false);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | BOOKINGS
        |--------------------------------------------------------------------------
        |
        | DI SINI MUNCUL SLOT TERSEDIA:
        | - user pilih barber
        | - user pilih tanggal
        | - sistem hitung slot kosong → user pilih jam
        |
        |--------------------------------------------------------------------------
        */
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code')->unique();

            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('barber_id')->nullable()->constrained('barbers')->nullOnDelete();

            $table->date('date'); // User pilih tanggal
            $table->time('time'); // User pilih slot jam kosong (hasil perhitungan backend)

            $table->enum('status', [
                'pending',
                'confirmed',
                'checkin',
                'completed',
                'canceled'
            ])->default('pending');

            // Harga disimpan per booking (untuk menjaga histori harga)
            $table->integer('service_price')->default(0);
            $table->integer('barber_price')->default(0);
            $table->integer('total_price')->default(0);

            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->default('cash');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | REVIEWS
        |--------------------------------------------------------------------------
        */
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('barber_id')->constrained('barbers')->cascadeOnDelete();

            $table->integer('rating');
            $table->string('comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('barber_shifts');
        Schema::dropIfExists('services');
        Schema::dropIfExists('barbers');
        Schema::dropIfExists('users');
    }
};
