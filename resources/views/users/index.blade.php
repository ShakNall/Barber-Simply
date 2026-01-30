@extends('layouts.app')

@section('content')
    <div class="">

        <h3 class="fw-bold mb-4">Manajemen User</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambah">
            <i class="fe fe-user-plus"></i> Tambah User
        </button> --}}


        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-hover" id="usersTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $key => $u)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email ?? '-' }}</td>
                                <td>{{ $u->phone ?? '-' }}</td>
                                <td class="text-capitalize">{{ $u->role }}</td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal"
                                        data-target="#modalEdit{{ $u->id }}">
                                        <i class="fe fe-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#modalHapus{{ $u->id }}">
                                        <i class="fe fe-trash"></i>
                                    </button>

                                </td>
                            </tr>

                            <div class="modal fade" id="modalEdit{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('users.update', $u->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-content">

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">

                                                <label>Nama</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fe fe-user"></i></span>
                                                    </div>
                                                    <input name="name" value="{{ $u->name }}" class="form-control" required>
                                                </div>

                                                <label>Email</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                                    </div>
                                                    <input name="email" value="{{ $u->email }}" class="form-control">
                                                </div>

                                                <label>No HP</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fe fe-phone"></i></span>
                                                    </div>
                                                    <input name="phone" value="{{ $u->phone }}" class="form-control">
                                                </div>

                                                <label>Password (opsional)</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fe fe-lock"></i></span>
                                                    </div>
                                                    <input name="password" type="password" class="form-control"
                                                        placeholder="Kosongkan jika tidak diubah">
                                                </div>

                                                {{-- <label>Role</label>
                                                <select name="role" class="form-control">
                                                    @foreach (['admin', 'owner', 'customer', 'barber'] as $role)
                                                    <option value="{{ $role }}" {{ $u->role == $role ? 'selected' : '' }}>
                                                        {{ ucfirst($role) }}
                                                    </option>
                                                    @endforeach
                                                </select> --}}

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-warning">Update</button>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="modalHapus{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form method="POST" action="{{ route('users.destroy', $u->id) }}">
                                        @csrf
                                        @method('DELETE')

                                        <div class="modal-content">

                                            <div class="modal-header ">
                                                <h5 class="modal-title">Hapus User</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                Hapus user <b>{{ $u->name }}</b> ?
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

        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="modal-content">

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Tambah User</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">

                            <label>Nama</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fe fe-user"></i></span>
                                </div>
                                <input name="name" class="form-control" required>
                            </div>

                            <label>Email</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fe fe-mail"></i></span>
                                </div>
                                <input name="email" class="form-control">
                            </div>

                            <label>No HP</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fe fe-phone"></i></span>
                                </div>
                                <input name="phone" class="form-control">
                            </div>

                            <label>Password</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fe fe-lock"></i></span>
                                </div>
                                <input name="password" type="password" class="form-control" required>
                            </div>

                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                                <option value="customer">Customer</option>
                                <option value="barber">Barber</option>
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

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#usersTable').DataTable({
                    pageLength: 20,
                    lengthMenu: [10, 20, 50, 100],
                    ordering: true,
                    responsive: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        paginate: {
                            previous: "‹",
                            next: "›"
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection