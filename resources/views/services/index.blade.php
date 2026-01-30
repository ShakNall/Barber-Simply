@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <h3 class="fw-bold mb-4">Manajemen Services</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tombol Tambah --}}
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambah">
            <i class="fe fe-plus"></i> Tambah Service
        </button>

        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Layanan</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Deskripsi</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($services as $key => $s)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                {{-- FOTO SERVICE --}}
                                <td>
                                    <img src="{{ $s->image ? asset('storage/' . $s->image) : asset('images/default-service.jpg') }}"
                                        width="60" height="60" class="rounded">
                                </td>

                                <td>{{ $s->name }}</td>
                                <td>Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                                <td>{{ $s->duration }} menit</td>
                                <td>{{ $s->description ?? '-' }}</td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#modalEdit{{ $s->id }}">
                                        <i class="fe fe-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#modalHapus{{ $s->id }}">
                                        <i class="fe fe-trash"></i>
                                    </button>
                                </td>
                            </tr>



                            {{-- ===================== MODAL EDIT ===================== --}}
                            <div class="modal fade" id="modalEdit{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" enctype="multipart/form-data"
                                        action="{{ route('services.update', $s->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Service</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">

                                                <label>Nama Layanan</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text"><i class="fe fe-scissors"></i></span>
                                                    <input name="name" value="{{ $s->name }}" class="form-control" required>
                                                </div>

                                                <label>Harga</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text"><i class="fe fe-dollar-sign"></i></span>
                                                    <input name="price" type="number" value="{{ $s->price }}"
                                                        class="form-control" required>
                                                </div>

                                                <label>Durasi (menit)</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text"><i class="fe fe-clock"></i></span>
                                                    <input name="duration" type="number" value="{{ $s->duration }}"
                                                        class="form-control" required>
                                                </div>

                                                <label>Deskripsi</label>
                                                <textarea name="description"
                                                    class="form-control">{{ $s->description }}</textarea>

                                                <label class="mt-3">Foto Service</label>
                                                <input type="file" name="image" class="form-control">

                                                @if ($s->image)
                                                    <img src="{{ asset('storage/' . $s->image) }}" class="mt-2 rounded" width="80">
                                                @endif

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-warning">Update</button>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            </div>



                            {{-- ===================== MODAL HAPUS ===================== --}}
                            <div class="modal fade" id="modalHapus{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog  modal-dialog-centered">
                                    <form method="POST" action="{{ route('services.destroy', $s->id) }}">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Service</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                Yakin ingin menghapus layanan <b>{{ $s->name }}</b>?
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



        {{-- ===================== MODAL TAMBAH ===================== --}}
        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" action="{{ route('services.store') }}">
                    @csrf

                    <div class="modal-content">

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Tambah Service</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">

                            <label>Nama Layanan</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fe fe-scissors"></i></span>
                                <input name="name" class="form-control" required>
                            </div>

                            <label>Harga</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fe fe-dollar-sign"></i></span>
                                <input name="price" type="number" class="form-control" required>
                            </div>

                            <label>Durasi (menit)</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fe fe-clock"></i></span>
                                <input name="duration" type="number" class="form-control" required>
                            </div>

                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control"></textarea>

                            <label class="mt-3">Foto Service</label>
                            <input type="file" name="image" class="form-control">

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