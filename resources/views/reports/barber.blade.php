@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h3 class="fw-bold mb-4">
            Laporan Barber: {{ $barber->user->name }}
        </h3>

        {{-- FILTER --}}
        <div class="card shadow-sm p-3 mb-4">
            <form class="row" method="GET">

                <div class="col-md-4">
                    <label>Dari Tanggal</label>
                    <input type="date" class="form-control" name="from" value="{{ $from }}">
                </div>

                <div class="col-md-4">
                    <label>Sampai Tanggal</label>
                    <input type="date" class="form-control" name="to" value="{{ $to }}">
                </div>

                <div class="col-md-4 mt-4">
                    <button class="btn btn-primary btn-block">
                        Tampilkan
                    </button>
                </div>

            </form>
        </div>

        {{-- SUMMARY --}}
        <div class="row mb-4">

            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h6>Total Pendapatan</h6>
                    <h2 class="fw-bold">Rp {{ number_format($totalIncome) }}</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h6>Total Booking</h6>
                    <h2 class="fw-bold">{{ $totalBooking }}</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h6>Rata-rata / Booking</h6>
                    <h2 class="fw-bold">Rp {{ number_format($average) }}</h2>
                </div>
            </div>

        </div>

        {{-- TOP SERVICES --}}
        <div class="card shadow-sm p-3 mb-4">
            <h5 class="fw-bold">Service Terbanyak</h5>

            @if ($topServices->count() == 0)
                <p class="text-muted">Belum ada data.</p>
            @endif

            @foreach ($topServices as $s)
                <div class="border-bottom py-2 d-flex justify-content-between">
                    <span>{{ $s['service'] }} ({{ $s['count'] }}x)</span>
                    <b>Rp {{ number_format($s['income']) }}</b>
                </div>
            @endforeach

        </div>

        {{-- DETAIL BOOKING --}}
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3">Detail Booking</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Harga</th>
                        <th>Metode</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($bookings as $b)
                        <tr>
                            <td>{{ $b->date }} {{ $b->time }}</td>
                            <td>{{ $b->user->name }}</td>
                            <td>
                                @foreach ($b->services as $svc)
                                    <span class="badge badge-info">
                                        {{ $svc->service->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>Rp {{ number_format($b->total_price) }}</td>
                            <td>{{ ucfirst($b->payment_method) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
