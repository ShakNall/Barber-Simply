<div class="card shadow-sm">
    <div class="card-body table-responsive">

        <table class="table table-bordered align-middle text-center datatable" id="{{ $tableId }}">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Customer</th>
                    <th>Barber</th>
                    <th>Service</th>
                    <th>Sumber</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($rows as $b)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($b->date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($b->time)->format('H:i') }}</td>
                        <td>{{ $b->customer_label }}</td>
                        <td>{{ $b->barber->user->name }}</td>
                        <td>
                            @foreach ($b->services as $svc)
                                <span class="badge badge-info">{{ $svc->service->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $b->source === 'walk_in' ? 'badge-dark' : 'badge-primary' }}">
                                {{ strtoupper($b->source) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ statusBadge($b->status) }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($b->total_price) }}</td>
                        <td>
                            <div class="d-flex justify-content-between align-items-center text-nowrap">

                                {{-- LEFT ACTIONS --}}
                                <div>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus') }}"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $b->id }}">

                                        @if ($b->status === 'pending')
                                            <button name="status" value="confirmed" class="btn btn-info btn-sm">
                                                Confirm
                                            </button>
                                        @endif

                                        @if ($b->status === 'confirmed')
                                            <button name="status" value="checkin" class="btn btn-primary btn-sm">
                                                Check-in
                                            </button>
                                        @endif

                                        @if ($b->status === 'checkin')
                                            <button type="button" onclick="openPaymentModal({{ $b->id }})"
                                                class="btn btn-success btn-sm">
                                                Bayar
                                            </button>
                                        @endif
                                    </form>

                                    @if (!in_array($b->status, ['completed', 'canceled']))
                                        <button class="btn btn-warning btn-sm"
                                            onclick="openServiceModal({{ $b->id }}, @json($b->services->pluck('service_id')))">
                                            Edit Service
                                        </button>

                                        <button class="btn btn-secondary btn-sm"
                                            onclick="openBarberModal({{ $b->id }}, {{ $b->barber_id }})">
                                            Ganti Kapster
                                        </button>
                                    @endif
                                </div>

                                {{-- RIGHT ACTION (CANCEL) --}}
                                @if (!in_array($b->status, ['completed', 'canceled']))
                                    <button type="button" class="btn btn-danger btn-sm ms-2"
                                        onclick="openCancelModal({{ $b->id }})">
                                        Cancel
                                    </button>
                                @endif

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>
