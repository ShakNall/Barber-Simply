@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h3 class="fw-bold mb-4">Profil Saya</h3>

        <div class="card p-4 shadow-sm">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                </div>

                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                </div>

                <div class="form-group">
                    <label>Email (opsional)</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                </div>

                <button class="btn btn-primary mt-3">Simpan Perubahan</button>

            </form>
        </div>

    </div>
@endsection
