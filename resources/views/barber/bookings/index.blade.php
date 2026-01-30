@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h3 class="fw-bold mb-4">Manajemen Layanan</h3>

        @foreach ($bookings as $b)
            <div class="card shadow-sm mb-3 p-3">

                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="mb-1">
                            @foreach ($b->services as $svc)
                                <span class="badge badge-info">
                                    {{ $svc->service->name }}
                                </span>
                            @endforeach
                        </h5>
                        <p class="text-muted mb-1">Customer: <b>{{ $b->user->name }}</b></p>
                        <p class="mb-1">Tanggal: <b>{{ $b->date }}</b></p>
                        <p class="mb-1">Jam: <b>{{ $b->time }}</b></p>
                    </div>

                    <div class="text-right">
                        <span class="badge badge-info">{{ ucfirst($b->status) }}</span>
                    </div>
                </div>

            </div>
        @endforeach

        @if ($bookings->count() == 0)
            <div class="alert alert-info">Belum ada booking.</div>
        @endif

    </div>
@endsection