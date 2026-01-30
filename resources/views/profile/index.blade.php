@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h3 class="fw-bold mb-4">Profil Saya</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card p-4 shadow-sm">

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="form-group mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                </div>

                <div class="form-group mb-3">
                    <label>No HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                </div>

                <div class="form-group mb-3">
                    <label>Email (Opsional)</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>

                <div class="form-group mb-3">
                    <label>Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                {{-- KHUSUS BARBER --}}
                @if ($barber)
                    <hr>
                    <h5 class="fw-bold mb-3">Informasi Barber</h5>

                    <div class="form-group mb-3">
                        <label>Nama Panggilan</label>
                        <input type="text" name="nickname" class="form-control" value="{{ $barber->nickname }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>Keahlian</label>
                        <input type="text" name="speciality" class="form-control" value="{{ $barber->speciality }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>Harga Jasa Barber (override)</label>
                        <input type="number" name="price" class="form-control" value="{{ $barber->price }}">
                    </div>
                @endif

                <button class="btn btn-primary w-100 mt-3">Simpan Perubahan</button>

            </form>
        </div>
    </div>
@endsection
