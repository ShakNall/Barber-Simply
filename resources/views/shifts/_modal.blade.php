{{-- ================= MODAL EDIT SHIFT ================= --}}
<div class="modal fade" id="modalShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('shifts.store') }}" class="modal-content border-0 shadow">
            @csrf

            <input type="hidden" name="barber_id" id="barber_id">
            <input type="hidden" name="day_of_week" id="day_of_week">
            <input type="hidden" name="week_number" id="week_number">

            {{-- HEADER --}}
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fe fe-clock me-1"></i> Atur Shift Barber
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="is_day_off" id="is_day_off" class="form-control">
                        <option value="0">Masuk</option>
                        <option value="1">Libur</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Shift</label>
                    <select name="shift_id" id="shift_id" class="form-control">
                        <option value="">-- Pilih Shift --</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="alert alert-info small mb-0">
                    💡 Pilih <b>Libur</b> jika barber tidak masuk di hari tersebut.
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary fw-semibold">
                    <i class="fe fe-save me-1"></i> Simpan Shift
                </button>
            </div>

        </form>
    </div>
</div>