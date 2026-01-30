@extends('layouts.app')

@section('content')
    <style>
        .week-card {
            border-radius: 14px;
        }

        .shift-table th {
            font-size: 11px;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #6c757d;
            border-bottom: 1px solid #f1f3f5;
        }

        .shift-table td {
            padding: 14px 10px;
            border-color: #f1f3f5;
        }

        .badge-shift {
            background: #e9f7ef;
            color: #198754;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
        }

        .badge-off {
            color: #adb5bd;
            font-size: 12px;
            font-weight: 500;
        }
    </style>

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="mb-4">
            <h3 class="fw-bold mb-1">Shift Saya</h3>
            <small class="text-muted">Jadwal kerja saya selama 4 minggu</small>
        </div>

        {{-- WEEK CARDS --}}
        @foreach ($shifts as $week => $items)
            <div class="card week-card shadow-sm border-0 mb-4">

                {{-- HEADER CARD --}}
                <div class="card-header bg-white border-0 d-flex align-items-center">
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                        Minggu {{ $week }}
                    </span>
                </div>

                {{-- BODY --}}
                <div class="card-body table-responsive p-0">
                    <table class="table shift-table align-middle text-center mb-0">

                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($days as $day)
                                @php
                                    $shift = $items->firstWhere('day_of_week', $day);
                                @endphp

                                <tr>
                                    <td class="fw-semibold">
                                        {{ ucfirst($day) }}
                                    </td>

                                    <td>
                                        @if ($shift && !$shift->is_day_off)
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if (!$shift || $shift->is_day_off)
                                            <span class="badge-off">OFF</span>
                                        @else
                                            <span class="badge rounded-pill badge-shift">Masuk</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        @endforeach

    </div>
@endsection
