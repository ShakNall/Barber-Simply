@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h3 class="fw-bold mb-4">Manajemen Kapster</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambah">
            <i class="fe fe-plus"></i> Tambah Kapster
        </button>

        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email / No. HP</th>
                            <th>Nickname</th>
                            <th>Speciality</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($barbers as $i => $b)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <img src="{{ $b->image ? asset('storage/' . $b->image) : asset('images/default-barber.jpg') }}"
                                        width="60" height="60" class="rounded">
                                </td>
                                <td>{{ $b->user->name }}</td>
                                <td>
                                    {{ $b->user->email }} <br>
                                    <small class="text-muted">{{ $b->user->phone ?? '-' }}</small>
                                </td>
                                <td>{{ $b->nickname ?? '-' }}</td>
                                <td>{{ $b->speciality ?? '-' }}</td>
                                <td>Rp {{ number_format($b->price) }}</td>
                                <td>
                                    <span class="badge {{ $b->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#modalEdit{{ $b->id }}">
                                        <i class="fe fe-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#modalHapus{{ $b->id }}">
                                        <i class="fe fe-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- ================= EDIT ================= --}}
                            <div class="modal fade" id="modalEdit{{ $b->id }}">
                                <div class="modal-dialog">
                                    <form method="POST" enctype="multipart/form-data"
                                        action="{{ route('barbers.update', $b->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">Edit Barber</h5>
                                                <button class="close" data-dismiss="modal">×</button>
                                            </div>

                                            <div class="modal-body">
                                                <label class="fw-bold">Nama</label>
                                                <input name="name" value="{{ $b->user->name }}" class="form-control mb-2" required>

                                                <label class="fw-bold">Email</label>
                                                <input name="email" type="email" value="{{ $b->user->email }}" class="form-control mb-2" required>

                                                <label class="fw-bold">No. HP (Hanya Angka)</label>
                                                <input name="phone" type="text" value="{{ $b->user->phone }}" 
                                                    class="form-control mb-2" placeholder="Contoh: 08123456789"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>

                                                <label class="fw-bold">Nickname</label>
                                                <input name="nickname" value="{{ $b->nickname }}" class="form-control mb-2">

                                                <label class="fw-bold">Speciality</label>
                                                <input name="speciality" value="{{ $b->speciality }}" class="form-control mb-2">

                                                <label class="fw-bold">Harga</label>
                                                <input name="price" type="number" value="{{ $b->price }}"
                                                    class="form-control mb-2" required>

                                                <label class="fw-bold">Foto</label>
                                                <input type="file" name="image" class="form-control mb-2">

                                                <label class="fw-bold">Status</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="1" {{ $b->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$b->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- ================= HAPUS ================= --}}
                            <div class="modal fade" id="modalHapus{{ $b->id }}">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form method="POST" action="{{ route('barbers.destroy', $b->id) }}">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5>Hapus Barber</h5>
                                            </div>
                                            <div class="modal-body">
                                                Hapus barber <b>{{ $b->user->name }}</b>?
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button class="btn btn-danger">Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        {{-- ================= TAMBAH ================= --}}
        <div class="modal fade" id="modalTambah">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" action="{{ route('barbers.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Tambah Barber</h5>
                            <button class="close" data-dismiss="modal">×</button>
                        </div>

                        <div class="modal-body">
                            <h6 class="fw-bold text-primary">Akun Barber</h6>
                            <input name="name" class="form-control mb-2" placeholder="Nama Lengkap" required>
                            <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
                            
                            {{-- Input No HP Baru --}}
                            <input name="phone" type="text" class="form-control mb-2" 
                                placeholder="No. HP (Contoh: 08123456789)" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                            
                            <input name="password" type="password" class="form-control mb-3" placeholder="Password" required>

                            <hr>

                            <h6 class="fw-bold text-primary">Data Barber</h6>
                            <input name="nickname" class="form-control mb-2" placeholder="Nickname">
                            <input name="speciality" class="form-control mb-2" placeholder="Speciality (Contoh: Fade, Pompadour)">
                            <input name="price" type="number" class="form-control mb-2" placeholder="Harga Jasa" required>
                            
                            <label class="small text-muted mb-1">Foto Profil</label>
                            <input type="file" name="image" class="form-control mb-2">

                            <label class="small text-muted mb-1">Status Keaktifan</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Barber</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection