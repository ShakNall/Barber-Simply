@php
    function statusBadge($status)
    {
        return [
            'pending' => 'badge badge-warning',
            'confirmed' => 'badge badge-info',
            'checkin' => 'badge badge-primary',
            'completed' => 'badge badge-success',
            'canceled' => 'badge badge-danger',
        ][$status] ?? 'badge badge-secondary';
    }
@endphp

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="fw-bold mb-0">Data Booking & Order</h3>

                <button class="btn btn-success" data-toggle="modal" data-target="#modalWalkIn">
                    <i class="fe fe-plus"></i> Walkin
                </button>
            </div>
        </div>

        <div class="card p-3 shadow-sm mb-4">
            <form method="GET" class="row align-items-end">
                <div class="col-md-2 mb-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        @foreach (['pending', 'confirmed', 'checkin', 'completed', 'canceled'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label>Barber</label>
                    <select name="barber" class="form-control">
                        <option value="">Semua</option>
                        @foreach ($barbers as $b)
                            <option value="{{ $b->id }}" {{ request('barber') == $b->id ? 'selected' : '' }}>
                                {{ $b->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label>Dari</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>

                <div class="col-md-2 mb-2">
                    <label>Sampai</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>

                {{-- FILTER --}}
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary w-100">
                        <i class="fe fe-search"></i> Filter
                    </button>
                </div>

                {{-- EXPORT --}}
                <div class="col-md-2 mb-2">
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-primary dropdown-toggle w-100" data-toggle="dropdown">
                            <i class="fe fe-download"></i> Export Excel
                        </button>
                        <div class="dropdown-menu dropdown-menu-right w-100">
                            <a class="dropdown-item export-online" href="#">
                                📱 Booking Online
                            </a>
                            <a class="dropdown-item export-walkin" href="#">
                                🚶 Walk-in
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item export-all" href="#">
                                📦 Online + Walk-in
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <ul class="nav nav-tabs mb-3" id="bookingTabs">
            <li class="nav-item">
                <button class="nav-link active" id="tab-antrian-btn" data-toggle="tab" data-target="#tab-antrian">
                    🔥 Antrian Hari Ini
                </button>
            </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-online-btn" data-toggle="tab" data-target="#tab-online">
                        📱 Booking Online
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-walkin-btn" data-toggle="tab" data-target="#tab-walkin">
                        🚶 Walkin
                    </button>
                </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-antrian">
                @include('admin.bookings._table', [
                    'rows' => $antrianHariIni,
                    'tableId' => 'table-antrian',
                ])
            </div>

            <div class="tab-pane fade" id="tab-online">
                @include('admin.bookings._table', [
                    'rows' => $bookingsOnline,
                    'tableId' => 'table-online',
                ])
            </div>

            <div class="tab-pane fade" id="tab-walkin">
                @include('admin.bookings._table', [
                    'rows' => $bookingsWalkin,
                    'tableId' => 'table-walkin',
                ])
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalWalkIn" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.bookings.walkin') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Order Walk-in</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <label>Nama Customer</label>
                        <input type="text" name="customer_name" class="form-control mb-2" required>

                        <label>Kapster</label>
                        <select name="barber_id" class="form-control mb-2" required>
                            @foreach ($barbers as $b)
                                <option value="{{ $b->id }}">{{ $b->user->name }}</option>
                            @endforeach
                        </select>

                       <label>Service</label>

                        @foreach ($services as $s)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    name="service_ids[]"
                                    value="{{ $s->id }}"
                                    id="walkin_svc{{ $s->id }}">

                                <label class="form-check-label" for="walkin_svc{{ $s->id }}">
                                    {{ $s->name }} — Rp {{ number_format($s->price) }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success w-100">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.bookings.complete') }}" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="payment_booking_id">

                <div class="modal-header">
                    <h5>Pilih Metode Pembayaran</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <select name="payment_method" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success w-100">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>



    <div class="modal fade" id="modalEditService" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.bookings.updateServices') }}" class="modal-content">
                @csrf

                <input type="hidden" name="booking_id" id="edit_booking_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Service Booking</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <label>Service</label>

                    @foreach ($services as $s)
                        <div class="form-check">
                            <input class="form-check-input service-checkbox" type="checkbox" name="service_ids[]"
                                value="{{ $s->id }}" id="svc{{ $s->id }}">

                            <label class="form-check-label" for="svc{{ $s->id }}">
                                {{ $s->name }} — Rp {{ number_format($s->price) }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary w-100">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalChangeBarber" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.bookings.changeBarber') }}" class="modal-content">
                @csrf

                <input type="hidden" name="booking_id" id="change_barber_booking_id">

                <div class="modal-header">
                    <h5 class="modal-title">Ganti Kapster</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <label>Pilih Kapster</label>
                    <select name="barber_id" class="form-control" required>
                        @foreach ($barbers as $barber)
                            <option value="{{ $barber->id }}">
                                {{ $barber->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary w-100">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin.bookings.updateStatus') }}" class="modal-content">
                @csrf
                <input type="hidden" name="id" id="cancel_booking_id">
                <input type="hidden" name="status" value="canceled">

                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body text-center">
                    <p class="fw-bold mb-1">Yakin ingin membatalkan booking ini?</p>
                    <small class="text-muted">
                        Status akan berubah menjadi <b>CANCEL</b> dan tidak bisa dikembalikan.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script>
        const TAB_KEY = 'booking_active_tab';

        document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function() {
                localStorage.setItem(TAB_KEY, this.id);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem(TAB_KEY);

            if (activeTab) {
                const tabButton = document.getElementById(activeTab);
                if (tabButton) {
                    $(tabButton).tab('show');
                }
            }
        });

        document.querySelector('#modalWalkIn form')?.addEventListener('submit', function() {
            localStorage.setItem(TAB_KEY, 'tab-walkin-btn');
        });

        function openPaymentModal(id) {
            document.getElementById('payment_booking_id').value = id
            $('#paymentModal').modal('show')
        }

        function openServiceModal(bookingId, selectedServices) {
            document.getElementById('edit_booking_id').value = bookingId;

            document.querySelectorAll('.service-checkbox').forEach(cb => {
                cb.checked = selectedServices.includes(parseInt(cb.value));
            });

            $('#modalEditService').modal('show');
        }

        function openBarberModal(bookingId, currentBarberId) {
            document.getElementById('change_barber_booking_id').value = bookingId;

            const select = document.querySelector('#modalChangeBarber select[name="barber_id"]');
            select.value = currentBarberId;

            $('#modalChangeBarber').modal('show');
        }

        function openCancelModal(bookingId) {
            document.getElementById('cancel_booking_id').value = bookingId;
            $('#cancelModal').modal('show');
        }


        const tables = {};

        $('.datatable').each(function() {
            const id = this.id;

            tables[id] = $(this).DataTable({
                paging: true,
                ordering: true,
                searching: true,
                responsive: true,
                language: {
                    emptyTable: "Tidak ada data"
                },
                dom: 'frtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Data Booking',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }]
            });
        });

        $('.export-excel').on('click', function() {
            const tableId = $(this).data('target');

            if (!tables[tableId]) {
                console.error('DataTable not found:', tableId);
                return;
            }

            tables[tableId].buttons('.buttons-excel').trigger();
        });

        $('button[data-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        });

        $('.export-online').on('click', function(e) {
            e.preventDefault();
            tables['table-online']?.buttons('.buttons-excel').trigger();
        });

        $('.export-walkin').on('click', function(e) {
            e.preventDefault();
            tables['table-walkin']?.buttons('.buttons-excel').trigger();
        });

        $('.export-all').on('click', function(e) {
            e.preventDefault();

            tables['table-online']?.buttons('.buttons-excel').trigger();
            setTimeout(() => {
                tables['table-walkin']?.buttons('.buttons-excel').trigger();
            }, 600);
        });
    </script>
@endpush
