@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h3 class="fw-bold mb-4">Profil Saya</h3>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-4 shadow-sm border-0">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    {{-- Jika Barber, kolom kiri 8, jika Admin/Customer kolom jadi 12 (Full) --}}
                    <div class="{{ $user->role === 'barber' ? 'col-md-8' : 'col-md-12' }}">
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">No HP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Email (Opsional)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">Password Baru (Kosongkan jika tidak ingin ganti)</label>
                            <input type="password" name="password" class="form-control" placeholder="******">
                        </div>
                    </div>

                    {{-- Foto Profil: HANYA MUNCUL UNTUK BARBER --}}
                    @if($user->role === 'barber')
                    <div class="col-md-4 text-center">
                        <label class="form-label fw-semibold d-block text-start text-md-center">Foto Profil</label>
                        <div class="mb-3">
                            @if($barber && $barber->image)
                                <img src="{{ asset('storage/' . $barber->image) }}"
                                     class="rounded-circle img-thumbnail shadow-sm"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                     class="rounded-circle img-thumbnail shadow-sm"
                                     style="width: 150px; height: 150px;">
                            @endif
                        </div>
                        <input type="file" name="image" class="form-control form-control-sm">
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, Max: 2MB</small>
                    </div>
                    @endif
                </div>

                {{-- INFORMASI KHUSUS BARBER --}}
                @if ($user->role === 'barber' && $barber)
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3 text-primary">Informasi Barber</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Nama Panggilan</label>
                                <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $barber->nickname) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Harga Jasa Barber</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="price" class="form-control" value="{{ old('price', $barber->price) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold">Keahlian / Deskripsi Singkat</label>
                        <input type="text" name="speciality" class="form-control" value="{{ old('speciality', $barber->speciality) }}" placeholder="Contoh: Fade, Pompadour, Beard Trim">
                    </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection