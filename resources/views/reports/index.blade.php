@extends('layouts.app')

@section('content')
    <div class="">
        <h2 class="fw-bold mb-4">Laporan Pendapatan</h2>

        {{-- FILTER --}}
        <div class="card shadow-sm p-3 mb-4">
            <form method="GET" class="row">

                <div class="col-md-4">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>

                <div class="col-md-4 mt-4">
                    <button class="btn btn-primary btn-block">
                        <i class="fe fe-search"></i> Tampilkan
                    </button>
                </div>

            </form>
        </div>

        {{-- SUMMARY --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card p-3 shadow-sm">
                    <h6>Total Pendapatan</h6>
                    <h2 class="fw-bold">Rp {{ number_format($totalIncome) }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm">
                    <h6>Cash</h6>
                    <h3 class="fw-bold">Rp {{ number_format($paymentBreakdown['cash']) }}</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm">
                    <h6>QRIS</h6>
                    <h3 class="fw-bold">Rp {{ number_format($paymentBreakdown['qris']) }}</h3>
                </div>
            </div>
        </div>

        {{-- INCOME PER BARBER --}}
        <div class="card shadow-sm p-3 mb-4">
            <h5 class="fw-bold">Pendapatan per Barber</h5>

            @foreach ($incomePerBarber as $b)
                <div class="border-bottom py-2 d-flex justify-content-between">
                    <span>{{ $b['name'] }}</span>
                    <b>Rp {{ number_format($b['income']) }}</b>
                </div>
            @endforeach
        </div>

        {{-- INCOME PER SERVICE --}}
        <div class="card shadow-sm p-3 mb-4">
            <h5 class="fw-bold">Pendapatan per Service</h5>

            @foreach ($incomePerService as $s)
                <div class="border-bottom py-2 d-flex justify-content-between">
                    <span>{{ $s['name'] }}</span>
                    <b>Rp {{ number_format($s['income']) }}</b>
                </div>
            @endforeach
        </div>


        {{-- DETAIL TABLE --}}
        <div class="card p-3 shadow-sm">
            <h5 class="fw-bold mb-3">Detail Transaksi</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Barber</th>
                        <th>Service</th>
                        <th>Metode</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($bookings as $b)
                        <tr>
                            <td>{{ $b->date }} {{ $b->time }}</td>
                            <td>{{ $b->user->name ?? $b->customer_name }}</td>
                            <td>{{ $b->barber->user->name }}</td>
                            <td>
                                @foreach ($b->services as $svc)
                                    <span class="badge badge-info">
                                        {{ $svc->service->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>{{ ucfirst($b->payment_method) }}</td>
                            <td>Rp {{ number_format($b->total_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection