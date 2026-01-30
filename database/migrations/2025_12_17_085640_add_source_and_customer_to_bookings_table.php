<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('source', ['online', 'walk_in'])
                ->default('online')
                ->after('booking_code');

            $table->string('customer_name')
                ->nullable()
                ->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['source', 'customer_name']);
        });
    }
};
