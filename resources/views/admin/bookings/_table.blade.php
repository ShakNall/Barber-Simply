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
                @php
                    /** * SORTING: Completed & Canceled tetap di bawah antrean hari ini
                     */
                    $sortedRows = $rows->sortBy(function($item) {
                        return ($item->status === 'completed' || $item->status === 'canceled') ? 1 : 0;
                    });

                    // Mencatat waktu selesai terakhir per barber per tanggal
                    $barberLastEndTimes = [];
                @endphp

                @foreach ($sortedRows as $b)
                    @php
                        $duration = $b->services->sum('duration');
                        $barberId = $b->barber_id;
                        $tanggalRow = $b->date; 
                        $key = $barberId . '_' . $tanggalRow;
                        
                        /** * LOGIKA JAM: 
                         * 1. Menyambung hanya jika walk_in dan tanggal sama.
                         * 2. Jam dihitung berdasarkan input asli jika baris sebelumnya tidak ada atau status sebelumnya cancel.
                         */
                        if ($b->source === 'walk_in' && isset($barberLastEndTimes[$key])) {
                            $start = $barberLastEndTimes[$key]->copy();
                        } else {
                            $start = \Carbon\Carbon::parse($b->time);
                        }

                        $end = $start->copy()->addMinutes($duration);
                        
                        /** * UPDATE RIWAYAT JAM:
                         * Jam selesai hanya dicatat jika statusnya BUKAN canceled.
                         * Jika canceled, maka $barberLastEndTimes[$key] tidak diupdate, 
                         * sehingga baris selanjutnya akan mengambil jam dari transaksi terakhir yang sukses.
                         */
                        if ($b->status !== 'canceled') {
                            $barberLastEndTimes[$key] = $end->copy();
                        }
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($b->date)->format('d M Y') }}</td>
                        
                        {{-- Jika cancel, jam mungkin tetap ditampilkan jam rencana awal --}}
                        <td class="{{ $b->status === 'canceled' ? 'text-decoration-line-through text-danger' : '' }}">
                            {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                        </td>

                        <td>{{ $b->customer_label }}</td>
                        <td>{{ $b->barber->user->name ?? 'Kapster dihapus' }}</td>
                        <td>
                            @foreach ($b->services as $svc)
                                <span class="badge badge-info">{{ $svc->service->name }}</span>
                            @endforeach
                            <br>Durasi: {{ $duration }} menit
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
                                <div>
                                    <form method="POST" action="{{ route('admin.bookings.updateStatus') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $b->id }}">
                                        @if ($b->status === 'pending')
                                            <button name="status" value="confirmed" class="btn btn-info btn-sm">Confirm</button>
                                        @endif
                                        @if ($b->status === 'confirmed')
                                            <button name="status" value="checkin" class="btn btn-primary btn-sm">Check-in</button>
                                        @endif
                                        @if ($b->status === 'checkin')
                                            <button type="button" onclick="openPaymentModal({{ $b->id }})" class="btn btn-success btn-sm">Bayar</button>
                                        @endif
                                    </form>

                                    @if (!in_array($b->status, ['completed', 'canceled']))
                                        <button class="btn btn-warning btn-sm" onclick="openServiceModal({{ $b->id }}, @json($b->services->pluck('service_id')))">Edit</button>
                                        <button class="btn btn-secondary btn-sm" onclick="openBarberModal({{ $b->id }}, {{ $b->barber_id }})">Ganti Kapster</button>
                                    @endif
                                </div>

                                @if (!in_array($b->status, ['completed', 'canceled']))
                                    <button type="button" class="btn btn-danger btn-sm ms-2" onclick="openCancelModal({{ $b->id }})">Cancel</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>