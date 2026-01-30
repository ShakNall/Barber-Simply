@extends('layouts.app')

@section('content')
    <div class="">

        {{-- ====================== CUSTOMER DASHBOARD ====================== --}}
        @if ($mode == 'customer')
            <h2 class="fw-bold mb-3">Halo, {{ auth()->user()->name }} 👋</h2>

            <div class="card bg-light shadow-md border-2 border-primary p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Antrian Hari Ini</h6>
                        <h2 class="fw-bold mb-0">{{ $todayQueues->count() }} Orang</h2>
                    </div>
                    <i class="fe fe-users style-size-lg"></i>
                </div>

                @if($todayQueues->count() > 0)
                    <div class="mt-3">
                        <p class="small mb-2">Jam Terisi:</p>
                        <div class="d-flex flex-wrap" style="gap: 5px;">
                            @foreach($todayQueues as $q)
                                <span class="badge badge-primary">
                                    {{ \Carbon\Carbon::parse($q->time)->format('H:i') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-3 small text-center">
                        Belum ada antrian hari ini.
                    </div>
                @endif
            </div>

            <h4 class="fw-bold">Riwayat Booking Kamu</h4>

            <div class="card shadow-sm p-3">
                @forelse ($myBookings as $b)
                    <div class="border-bottom py-2">
                        <div class="mt-1">
                            @foreach ($b->services as $svc)
                                <span class="badge badge-info">
                                    {{ $svc->service->name }}
                                </span>
                            @endforeach
                        </div>
                        <div class="text-muted small mt-1">
                            {{ $b->date }} / {{ $b->time }}
                        </div>
                        <div>
                            Status: <span class="badge badge-secondary">{{ $b->status }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Belum ada booking.</p>
                @endforelse
            </div>

            <div class="mt-4">
                <a href="/booking/create" class="btn btn-primary btn-lg btn-block">
                    Buat Booking Baru
                </a>
            </div>
        @endif

        {{-- ====================== ADMIN DASHBOARD ====================== --}}
        @if ($mode == 'admin')
            <h2 class="fw-bold mb-4">Dashboard Admin</h2>

            {{-- KPI --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-3">
                        <h6>Total Booking Hari Ini</h6>
                        <h2 class="fw-bold">{{ $totalBookingsToday }}</h2>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-3">
                        <h6>Pendapatan Hari Ini</h6>
                        <h2 class="fw-bold">Rp {{ number_format($incomeToday) }}</h2>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-3">
                        <h6>Customer Baru Hari Ini</h6>
                        <h2 class="fw-bold">{{ $newCustomersToday }}</h2>
                    </div>
                </div>
            </div>

            {{-- STATUS --}}
            <div class="row mt-3">
                @foreach ($statusCounts as $key => $value)
                    <div class="col-md-3">
                        <div class="card p-3 shadow-sm text-center">
                            <h6 class="text-capitalize">{{ $key }}</h6>
                            <h3>{{ $value }}</h3>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- CHART 7 Hari --}}
            <div class="card shadow-sm p-3 mt-4">
                <h5 class="fw-bold mb-3">Grafik Booking 7 Hari Terakhir</h5>
                <canvas id="bookingChart" height="100"></canvas>
            </div>

            {{-- CHART Income --}}
            <div class="card shadow-sm p-3 mt-4">
                <h5 class="fw-bold mb-3">Pendapatan 30 Hari Terakhir</h5>
                <canvas id="incomeChart" height="100"></canvas>
            </div>

            {{-- Recent Bookings --}}
            <div class="card shadow-sm p-3 mt-4">
                <h5 class="fw-bold mb-3">Booking Terbaru</h5>

                @forelse ($recentBookings as $b)
                    <div class="border-bottom py-2">

                        <div class="fw-semibold">
                            {{ $b->user->name ?? $b->customer_name }}
                        </div>

                        <div class="mt-1">
                            @foreach ($b->services as $svc)
                                <span class="badge badge-info">
                                    {{ $svc->service->name }}
                                </span>
                            @endforeach
                        </div>

                        <div class="text-muted small mt-1">
                            {{ \Carbon\Carbon::parse($b->date)->format('d F Y') }}
                            {{ \Carbon\Carbon::parse($b->time)->format('H:i') }}
                            &mdash;
                            Barber <b>{{ $b->barber->user->name }}</b>
                        </div>

                    </div>
                @empty
                    <div class="text-muted">Belum ada booking.</div>
                @endforelse
            </div>


            {{-- Barber Top --}}
            <div class="card shadow-sm p-3 mt-4">
                <h5 class="fw-bold mb-3">Top Barber (Paling Banyak Booking)</h5>

                @foreach ($barberTop as $barber)
                    <div class="border-bottom py-2">
                        <b>{{ $barber->user->name }}</b> — {{ $barber->bookings_count }} Booking
                    </div>
                @endforeach
            </div>

            {{-- ChartJS --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                new Chart(document.getElementById('bookingChart'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($last7days->pluck('date')) !!},
                        datasets: [{
                            label: 'Booking',
                            data: {!! json_encode($last7days->pluck('count')) !!},
                            borderColor: '#007bff',
                            fill: false
                        }]
                    }
                });

                new Chart(document.getElementById('incomeChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($last30days->pluck('date')) !!},
                        datasets: [{
                            label: 'Pendapatan',
                            data: {!! json_encode($last30days->pluck('income')) !!},
                            backgroundColor: '#28a745'
                        }]
                    }
                });
            </script>
        @endif

    </div>
@endsection