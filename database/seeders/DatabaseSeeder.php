<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BarberShift;
use App\Models\BookingService;
use App\Models\Review;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        BarberShift::truncate();
        Shift::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->createAdmins();
        $this->createCustomers();
        $this->createBarbers();
        $this->createServices();
        $this->createShifts();
        $this->createBarberWeeklyShifts();
    }

    /* -------------------------------------------------------------------------- */
    /*  ADMINS                                                                    */
    /* -------------------------------------------------------------------------- */
    private function createAdmins()
    {
        User::create([
            'name' => 'Admin One',
            'email' => 'admin@barbershop.com',
            'phone' => '085169765567',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*  CUSTOMERS                                                                 */
    /* -------------------------------------------------------------------------- */
    private function createCustomers()
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Customer $i",
                'email' => "customer$i@mail.com",
                'phone' => "0887000000$i",
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }
    }

    /* -------------------------------------------------------------------------- */
    /*  BARBERS (User + Barber table)                                             */
    /* -------------------------------------------------------------------------- */
    private function createBarbers()
    {
        $barbers = [
            ['BUDI',   'budi@barbershop.com',   'Senior Barber', '085213814968',    65000],
            ['ADI',    'adi@barbershop.com',    'Barber', '081328156587',           55000],
            ['DAFI',   'dafi@barbershop.com',   'Barber', '085728251307',           55000],
            ['ARI',    'ari@barbershop.com',    'Barber', '081657436226',           50000],
            ['JATI',   'jati@barbershop.com',   'Junior Barber','088219729020',     40000],
        ];

        foreach ($barbers as $index => $b) {
            $user = User::create([
                'name'     => 'By ' . $b[0],
                'email'    => $b[1],
                'phone'    => $b[3],
                'password' => Hash::make('password'),
                'role'     => 'barber',
            ]);

            Barber::create([
                'user_id'    => $user->id,
                'nickname'   => $b[0],
                'speciality' => $b[2],
                'price'      => $b[4],
                'is_active'  => true,
            ]);
        }
    }


    /* -------------------------------------------------------------------------- */
    /*  SERVICES                                                                  */
    /* -------------------------------------------------------------------------- */
    private function createServices()
    {
        $services = [

            [
                'Haircut',
                'k-perm.jpg',
                0,
                30,
                'Pijat kepala',
            ],
            [
                'Bleaching Coloring',
                'bleaching.jpg',
                65000,
                45,
                'Pewarnaan rambut (harga per step)',
            ],
            [
                'Hair Creambath',
                'creambath.jpg',
                40000,
                30,
                'Hairmask + pijat kepala 15 menit',
            ],
            [
                'Long Hair',
                'long-hair.jpg',
                20000,
                10,
                'Tambahan untuk rambut lebih dari 15 cm',
            ],
            [
                'Long Trim',
                'long-trim.jpg',
                10000,
                10,
                'Tambahan trim lebih dari 5 cm',
            ],
            [
                'Washing',
                'washing.jpg',
                10000,
                15,
                'Keramas, vitamin, dan styling',
            ],
            [
                'Shaving',
                'shaving.jpg',
                20000,
                20,
                'Jenggot / kumis (mulai 5k – 20k)',
            ],
            [
                'Home Service',
                'home-service.jpg',
                75000,
                0,
                'Layanan panggilan ke rumah',
            ],
            [
                'Curly Perm',
                'curly-perm.jpg',
                250000,
                90,
                'Keriting rambut',
            ],
            [
                'K-Perm',
                'k-perm.jpg',
                250000,
                90,
                'Rambut bergelombang',
            ],
        ];

        foreach ($services as $s) {
            Service::create([
                'name'        => $s[0],
                'image'       => $s[1],
                'price'       => $s[2],
                'duration'    => $s[3],
                'description' => $s[4],
            ]);
        }
    }


    private function createShifts()
    {
        Shift::insert([
            [
                'name' => 'Shift 1',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shift 2',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*  BARBER WEEKLY SHIFTS (FIXED)                                              */
    /* -------------------------------------------------------------------------- */
    private function createBarberWeeklyShifts()
    {
        $barbers = Barber::pluck('id')->values();
        $shifts  = Shift::pluck('id')->values();

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $totalBarbers = $barbers->count();

        if ($totalBarbers < 2) return;

        for ($week = 1; $week <= 4; $week++) {

            foreach ($days as $dayIndex => $day) {

                $offIndex = ($dayIndex + ($week - 1)) % $totalBarbers;
                $offBarberId = $barbers[$offIndex];

                foreach ($barbers as $barberId) {

                    $isWorking = $barberId != $offBarberId;

                    $shift = $isWorking
                        ? Shift::inRandomOrder()->first()
                        : null;

                    BarberShift::create([
                        'barber_id'   => $barberId,
                        'shift_id'    => $shift?->id,
                        'week_number' => $week,
                        'day_of_week' => $day,
                        'start_time'  => $shift?->start_time,
                        'end_time'    => $shift?->end_time,
                        'is_day_off'  => !$isWorking,
                    ]);
                }
            }
        }
    }
}
