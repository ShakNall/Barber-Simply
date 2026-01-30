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
                            <th>Email</th>
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
                                <td>{{ $b->user->email }}</td>
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
                                            <div class="modal-header bg-warning">
                                                <h5>Edit Barber</h5>
                                                <button class="close" data-dismiss="modal">×</button>
                                            </div>

                                            <div class="modal-body">
                                                <label>Nama</label>
                                                <input name="name" value="{{ $b->user->name }}" class="form-control mb-2">

                                                <label>Email</label>
                                                <input name="email" value="{{ $b->user->email }}" class="form-control mb-2">

                                                <label>Nickname</label>
                                                <input name="nickname" value="{{ $b->nickname }}" class="form-control mb-2">

                                                <label>Speciality</label>
                                                <input name="speciality" value="{{ $b->speciality }}" class="form-control mb-2">

                                                <label>Harga</label>
                                                <input name="price" type="number" value="{{ $b->price }}"
                                                    class="form-control mb-2">

                                                <label>Foto</label>
                                                <input type="file" name="image" class="form-control mb-2">

                                                <label>Status</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="1" {{ $b->is_active ? 'selected' : '' }}>Aktif
                                                    </option>
                                                    <option value="0" {{ !$b->is_active ? 'selected' : '' }}>Nonaktif
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-warning">Update</button>
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
                            <h5>Tambah Barber</h5>
                            <button class="close" data-dismiss="modal">×</button>
                        </div>

                        <div class="modal-body">
                            <h6 class="fw-bold">Akun Barber</h6>
                            <input name="name" class="form-control mb-2" placeholder="Nama" required>
                            <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
                            <input name="password" type="password" class="form-control mb-3" placeholder="Password"
                                required>

                            <hr>

                            <h6 class="fw-bold">Data Barber</h6>
                            <input name="nickname" class="form-control mb-2" placeholder="Nickname">
                            <input name="speciality" class="form-control mb-2" placeholder="Speciality">
                            <input name="price" type="number" class="form-control mb-2" placeholder="Harga" required>
                            <input type="file" name="image" class="form-control mb-2">

                            <select name="is_active" class="form-control">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection