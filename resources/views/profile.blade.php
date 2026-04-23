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
                    <label class="fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">No. Telepon</label>
                    <input type="text" 
                           name="phone" 
                           class="form-control" 
                           value="{{ auth()->user()->phone }}" 
                           placeholder="Contoh: 08123456789"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           required>
                    <small class="text-muted">Hanya masukkan angka saja.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Email (opsional)</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                </div>

                <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>

            </form>
        </div>

    </div>
@endsection