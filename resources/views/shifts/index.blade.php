@extends('layouts.app')

@section('content')
    <style>
        /* ====== GLOBAL ====== */
        .page-subtitle {
            font-size: 13px;
            color: #6c757d;
        }

        /* ====== CARD ====== */
        .week-card {
            border-radius: 14px;
        }

        .week-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f3f5;
        }

        /* ====== TABLE ====== */
        .shift-table th {
            font-size: 11px;
            letter-spacing: .6px;
            color: #6c757d;
            text-transform: uppercase;
            border-bottom: 1px solid #f1f3f5;
            padding: 12px;
        }

        .shift-table td {
            padding: 14px 10px;
            border-color: #f1f3f5;
        }

        .shift-table tbody tr:hover {
            background-color: #fafbfc;
        }

        /* ====== BADGES ====== */
        .badge-shift {
            background: #e9f7ef;
            color: #198754;
            font-weight: 500;
            font-size: 12px;
            padding: 6px 12px;
        }

        .badge-off {
            color: #adb5bd;
            font-weight: 500;
            font-size: 12px;
            letter-spacing: .3px;
        }

        /* ====== AVATAR ====== */
        .avatar-soft {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        /* ====== CELL ====== */
        .shift-cell:hover {
            background: #f8f9fa;
        }
    </style>

    <div class="container-fluid">
        {{-- ================= MASTER SHIFT ================= --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Master Shift</h5>
                <button class="btn btn-primary btn-sm" onclick="openCreateShift()">
                    + Tambah Shift
                </button>
            </div>

            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Jam</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shifts as $shift)
                            <tr>
                                <td>{{ $shift->name }}</td>
                                <td>{{ $shift->start_time }} - {{ $shift->end_time }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick='openEditShift(@json($shift))'>
                                        Edit
                                    </button>

                                    <button class="btn btn-danger btn-sm"
                                        onclick="openDeleteShiftModal('{{ route('master.shifts.destroy', $shift->id) }}')">
                                        Hapus
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        <div class="modal fade" id="modalMasterShift" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="formMasterShift" class="modal-content">
                    @csrf
                    <input type="hidden" name="_method" id="methodMaster">

                    <div class="modal-header">
                        <h5 class="modal-title" id="titleMaster">Master Shift</h5>
                        <button class="btn-close" data-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Shift</label>
                            <input type="text" name="name" id="ms_name" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label>Mulai</label>
                                <input type="time" name="start_time" id="ms_start" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Selesai</label>
                                <input type="time" name="end_time" id="ms_end" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCancelMaster">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteShiftModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" id="deleteShiftForm" class="modal-content">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header ">
                        <h5 class="modal-title">Konfirmasi Hapus Shift</h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center">
                        <p class="fw-bold mb-1">
                            Yakin ingin menghapus master shift ini?
                        </p>
                        <small class="text-muted">
                            Shift yang dihapus tidak bisa digunakan di jadwal.
                        </small>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Ya, Hapus
                        </button>
                    </div>

                </form>
            </div>
        </div>



        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Jadwal Shift Barber</h3>
                <div class="page-subtitle">Pengaturan shift mingguan (4 minggu)</div>
            </div>

            <form action="{{ route('shifts.rolling') }}" method="POST"
                onsubmit="return confirm('Generate jadwal otomatis 4 minggu?')">
                @csrf
                <button class="btn btn-warning fw-semibold">
                    <i class="fe fe-refresh-cw"></i> Rolling 4 Minggu
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $weeks = [1, 2, 3, 4];
            function inisial($nama)
            {
                return collect(explode(' ', $nama))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('');
            }
        @endphp

        {{-- ================= WEEK CARDS ================= --}}
        @foreach ($weeks as $week)
            <div class="card week-card shadow-sm border-0 mb-4">

                {{-- HEADER CARD --}}
                <div class="week-header d-flex align-items-center bg-white">
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 me-3">
                        Minggu {{ $week }}
                    </span>
                    <span class="text-muted small">Klik sel untuk mengatur shift</span>
                </div>

                {{-- BODY --}}
                <div class="card-body table-responsive p-0">
                    <table class="table shift-table align-middle text-center mb-0">

                        <thead>
                            <tr>
                                <th class="text-start font-weight-bold ps-4">Barber</th>
                                @foreach ($days as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($barbers as $barber)
                                <tr>
                                    {{-- BARBER --}}
                                    <td class="text-start text-nowrap fw-semibold ps-4">
                                        <span class="avatar-soft rounded-circle me-2"
                                            style="width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center">
                                            {{ inisial($barber->user->name) }}
                                        </span>
                                        {{ $barber->user->name }}
                                    </td>

                                    {{-- DAYS --}}
                                    @foreach ($days as $day)
                                        @php
                                            $shift = $barber->shifts
                                                ->where('week_number', $week)
                                                ->firstWhere('day_of_week', $day);
                                        @endphp

                                        <td class="shift-cell" data-barber="{{ $barber->id }}" data-week="{{ $week }}"
                                            data-day="{{ $day }}" data-libur="{{ $shift?->is_day_off ?? 0 }}"
                                            data-shift_id="{{ $shift?->shift_id ?? null }}" style=" cursor:pointer">

                                            @if (!$shift || $shift->is_day_off)
                                                <span class="badge-off">OFF</span>
                                            @else
                                                <span class="badge rounded-pill badge-shift">
                                                    {{ $shift->shift->name }}
                                                    <small class="d-block text-muted">
                                                        {{ $shift->shift->start_time }} - {{ $shift->shift->end_time }}
                                                    </small>
                                                </span>
                                            @endif

                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        @endforeach

    </div>

    @include('shifts._modal')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ===== SHIFT SCHEDULE ELEMENTS =====
            const barber_id = document.getElementById('barber_id');
            const day_of_week = document.getElementById('day_of_week');
            const week_number = document.getElementById('week_number');
            const is_day_off = document.getElementById('is_day_off');
            const shift_id = document.getElementById('shift_id');
            const modalEl = document.getElementById('modalShift');

            // ===== CLICK CELL =====
            document.querySelectorAll('.shift-cell').forEach(cell => {
                cell.addEventListener('click', () => {

                    const isLibur = cell.dataset.libur === '1';

                    barber_id.value = cell.dataset.barber;
                    day_of_week.value = cell.dataset.day;
                    week_number.value = cell.dataset.week;

                    is_day_off.value = isLibur ? '1' : '0';

                    shift_id.value = cell.dataset.shift_id ?? '';
                    toggleShift(isLibur);

                    new bootstrap.Modal(modalEl).show();
                });
            });

            function toggleShift(isLibur) {
                shift_id.disabled = isLibur;
                if (isLibur) shift_id.value = '';
            }

            is_day_off.addEventListener('change', function () {
                toggleShift(this.value === '1');
            });

            // ================= MASTER SHIFT =================

            const modalMaster = new bootstrap.Modal(
                document.getElementById('modalMasterShift')
            );

            const formMaster = document.getElementById('formMasterShift');
            const methodMaster = document.getElementById('methodMaster');
            const titleMaster = document.getElementById('titleMaster');

            const ms_name = document.getElementById('ms_name');
            const ms_start = document.getElementById('ms_start');
            const ms_end = document.getElementById('ms_end');

            // ===== CREATE =====
            window.openCreateShift = function () {
                formMaster.action = "{{ route('master.shifts.store') }}";
                methodMaster.value = '';

                ms_name.value = '';
                ms_start.value = '';
                ms_end.value = '';

                titleMaster.innerText = 'Tambah Master Shift';
                modalMaster.show();
            }

            // ===== EDIT =====
            window.openEditShift = function (shift) {
                formMaster.action = `/master/shifts/${shift.id}`;
                methodMaster.value = 'PUT';

                ms_name.value = shift.name;
                ms_start.value = shift.start_time;
                ms_end.value = shift.end_time;

                titleMaster.innerText = 'Edit Master Shift';
                modalMaster.show();
            }

            window.openDeleteShiftModal = function (action) {
                const form = document.getElementById('deleteShiftForm');
                form.action = action;

                new bootstrap.Modal(
                    document.getElementById('deleteShiftModal')
                ).show();
            }

            const btnCancelMaster = document.getElementById('btnCancelMaster');

            btnCancelMaster.addEventListener('click', function () {
                modalMaster.hide();

                formMaster.reset();
                methodMaster.value = '';
            });

        });
    </script>


@endsection