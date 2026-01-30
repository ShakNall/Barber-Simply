@php
    function statusBadge($status)
    {
        return match ($status) {
            'pending' => 'badge bg-warning',
            'confirmed' => 'badge bg-info',
            'checkin' => 'badge bg-primary',
            'completed' => 'badge bg-success',
            'canceled' => 'badge bg-danger text-white',
            default => 'badge bg-secondary',
        };
    }
@endphp

@extends('layouts.app')

@section('content')
    <style>
        .booking-card {
            position: relative;
            border-radius: 12px;
            border: 1px solid #eee;
            padding: 18px;
            display: flex;
            margin-bottom: 15px;
            transition: 0.2s;
            background: #fff;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .booking-image {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
        }

        .service-badge {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 13px;
            margin: 3px 4px 3px 0;
            display: inline-block;
        }

        .btn-cancel {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
        }

        .action-btn {
            font-size: 14px;
            padding: 6px 12px;
        }
    </style>

    <div class="container mt-4">

        <h2 class="fw-bold mb-4">Riwayat Booking</h2>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Cari service atau barber...">
                <button class="btn btn-primary">
                    Cari
                </button>
            </div>
        </form>


        @forelse ($bookings as $booking)
            <div class="booking-card shadow-sm">

                {{-- CANCEL BUTTON (POJOK KANAN ATAS) --}}
                @if (in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="btn-cancel cancel-form">
                        @csrf
                        @method('PATCH')

                        <button type="button" class="btn btn-sm btn-danger action-btn btn-cancel-booking">
                            <i class="bi bi-x-lg"></i> Batalkan
                        </button>
                    </form>
                @endif


                {{-- FOTO BARBER --}}
                <img src="{{ asset('storage/' . ($booking->barber->image ?? 'default-barber.jpg')) }}" class="booking-image">

                {{-- INFO --}}
                <div style="flex:1">

                    {{-- SERVICES --}}
                    <h5 class="mb-2">
                        @foreach ($booking->services as $svc)
                            <span class="service-badge">
                                {{ $svc->service->name }}
                            </span>
                        @endforeach
                    </h5>

                    <p class="text-muted mb-1">
                        Barber: <b>{{ $booking->barber->user->name }}</b>
                    </p>

                    <p class="mb-1">
                        Tanggal: <b>{{ $booking->date->format('d M Y') }}</b><br>
                        Jam: <b>{{ \Carbon\Carbon::parse($booking->time)->format('H:i') }}</b>
                        <span class="text-muted">
                            ({{ $booking->total_service_duration }} menit)
                        </span>
                    </p>

                    <p class="mb-2">
                        Total:
                        <b class="text-primary">
                            Rp {{ number_format($booking->total_price) }}
                        </b>
                    </p>

                    <span class="{{ statusBadge($booking->status) }}">
                        {{ ucfirst($booking->status) }}
                    </span>

                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Belum ada booking.
            </div>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-cancel-booking').forEach(btn => {
                btn.addEventListener('click', function () {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Batalkan Booking?',
                        text: 'Booking yang dibatalkan tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Batalkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection