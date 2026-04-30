@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .service-card {
        border-radius: 10px;
        border: 2px solid #eee;
        padding: 10px;
        cursor: pointer;
        transition: .2s;
    }
    .service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0,0,0,.08);
    }
    .service-card.selected {
        border-color: #0d6efd;
        background: #f0f5ff;
    }

    .slot-btn {
        min-width: 90px;
        margin: 4px;
        border-radius: 8px;
    }
    .slot-btn.active {
        background: #198754;
        color: #fff;
        border-color: #198754;
    }

    .barber-card {
        border-radius: 10px;
        border: 2px solid #eee;
        padding: 10px;
        cursor: pointer;
        transition: .2s;
    }
    .barber-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0,0,0,.08);
    }
    .barber-card.selected {
        border-color: #0d6efd;
        background: #f0f5ff;
    }

    #slotSection, #barberSection, #serviceSection, #summarySection {
        display: none;
    }
</style>

<div class="container mt-4">
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0">Booking Manual (Admin)</h3>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                ← Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm">
        @csrf
        {{-- customer name --}}
        <input type="hidden" name="customer_name" id="formCustomerName" >
        <input type="hidden" name="admin_fee" value="5000">
        <input type="hidden" name="date"       id="formDate">
        <input type="hidden" name="time"       id="formTime">
        <input type="hidden" name="barber_id"  id="formBarber">
        <input type="hidden" name="total_price" id="formTotal">

        <div class="card p-4 shadow-sm mb-4">

            {{-- CUSTOMER NAME --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Nama Customer</label>
                    <input type="text" class="form-control" placeholder="Nama customer"
                           
                           oninput="document.getElementById('formCustomerName').value = this.value">
                </div>
            </div>

            {{-- TANGGAL & JAM --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="text" id="datePicker" class="form-control" placeholder="Pilih tanggal">
                </div>

                <div class="col-md-8 mb-3" id="slotSection">
                    <label class="form-label fw-semibold">Jam</label>
                    <div id="slotContainer" class="d-flex flex-wrap pt-1"></div>
                </div>
            </div>

            {{-- KAPSTER --}}
            <div id="barberSection" class="mb-3">
                <label class="form-label fw-semibold">Kapster</label>
                <div class="row" id="barberContainer"></div>
            </div>

            {{-- SERVICE --}}
            <div id="serviceSection" class="mb-3">
                <label class="form-label fw-semibold">Service <small class="text-muted">(bisa pilih lebih dari satu)</small></label>
                <div class="row">
                    @foreach ($services as $s)
                        <div class="col-md-4 mb-3">
                            <div class="service-card"
                                 data-id="{{ $s->id }}"
                                 data-name="{{ $s->name }}"
                                 data-price="{{ $s->price }}"
                                 data-duration="{{ $s->duration }}"
                                 data-type="{{ strtolower($s->name) === 'haircut' ? 'haircut' : 'normal' }}">
                                <img src="{{ asset('storage/' . ($s->image ?? 'default-service.jpg')) }}"
                                     class="w-100 rounded mb-2" style="height:130px;object-fit:cover;">
                                <h6 class="text-center mb-1">{{ $s->name }}</h6>
                                <p class="text-muted text-center mb-0" style="font-size:13px;">{{ $s->duration }} menit</p>
                                <p class="text-center mb-0" style="font-size:13px;">
                                    @if (strtolower($s->name) === 'haircut')
                                        <span class="text-muted">Mengikuti harga kapster</span>
                                    @else
                                        Rp {{ number_format($s->price) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SUMMARY --}}
            <div id="summarySection">
                <hr>
                <h5 class="fw-semibold mb-3">Ringkasan</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="130">Tanggal</td>
                                <td>: <span id="sumDate">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jam</td>
                                <td>: <span id="sumTime">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kapster</td>
                                <td>: <span id="sumBarber">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Service</td>
                                <td>: <span id="sumService">-</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted">Subtotal</td>
                                <td class="text-end">Rp <span id="sumSubtotal">0</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Biaya Admin</td>
                                <td class="text-end">Rp 5.000</td>
                            </tr>
                            <tr class="fw-bold text-primary">
                                <td>Total Bayar</td>
                                <td class="text-end">Rp <span id="sumGrandTotal">0</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-warning">
                    ⏰ Harap datang maksimal <b>15 menit</b> dari jam booking.
                    Jika terlambat, booking akan dibatalkan.
                </div>

                <button type="submit" class="btn btn-success w-100 mt-2" id="submitBtn" disabled>
                    Konfirmasi Booking
                </button>
            </div>

        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const ADMIN_FEE      = 5000;
    const CSRF           = "{{ csrf_token() }}";
    let selectedDate     = null;
    let selectedTime     = null;
    let selectedBarber   = null;
    let selectedServices = [];
    let totalPrice       = 0;
    let barberPrice      = 0;

    flatpickr("#datePicker", {
        minDate: new Date().fp_incr(1),
        dateFormat: "Y-m-d",
        disable: [date => date.getDay() === 5],
        onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
            document.getElementById("formDate").value   = dateStr;
            document.getElementById("sumDate").innerText = dateStr;

            // reset downstream
            resetFrom('slot');

            fetch("/booking/slots-by-date", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": CSRF, "Content-Type": "application/json" },
                body: JSON.stringify({ date: selectedDate })
            })
            .then(r => r.json())
            .then(slots => {
                const container = document.getElementById("slotContainer");
                container.innerHTML = "";

                document.getElementById("slotSection").style.display = "block";

                if (slots.length === 0) {
                    container.innerHTML = "<p class='text-muted'>Tidak ada slot tersedia di tanggal ini.</p>";
                    return;
                }

                slots.forEach(slot => {
                    const btn = document.createElement("button");
                    btn.type      = "button";
                    btn.className = "slot-btn btn btn-outline-primary";
                    btn.innerText = slot;

                    btn.onclick = () => {
                        document.querySelectorAll(".slot-btn").forEach(b => b.classList.remove("active"));
                        btn.classList.add("active");

                        selectedTime = slot;
                        document.getElementById("formTime").value    = slot;
                        document.getElementById("sumTime").innerText  = slot;

                        resetFrom('barber');

                        fetch("/booking/barbers", {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": CSRF, "Content-Type": "application/json" },
                            body: JSON.stringify({ date: selectedDate, time: selectedTime })
                        })
                        .then(r => r.json())
                        .then(barbers => {
                            const bc = document.getElementById("barberContainer");
                            bc.innerHTML = "";

                            document.getElementById("barberSection").style.display = "block";

                            if (barbers.length === 0) {
                                bc.innerHTML = "<p class='text-muted'>Tidak ada kapster tersedia di jam ini.</p>";
                                return;
                            }

                            barbers.forEach(b => {
                                bc.innerHTML += `
                                    <div class="col-md-4 mb-3">
                                        <div class="barber-card"
                                             data-id="${b.id}"
                                             data-name="${b.user.name}"
                                             data-price="${b.price}">
                                            <img src="/storage/${b.image ?? 'default-barber.jpg'}"
                                                 class="w-100 rounded mb-2" style="height:130px;object-fit:cover;">
                                            <h6 class="text-center mb-1">${b.user.name}</h6>
                                            <p class="text-center text-muted mb-0" style="font-size:13px;">
                                                Rp ${Number(b.price).toLocaleString('id-ID')}
                                            </p>
                                        </div>
                                    </div>`;
                            });
                        });
                    };

                    container.appendChild(btn);
                });
            });
        }
    });

    // Barber click (delegated)
    document.addEventListener("click", e => {
        const card = e.target.closest(".barber-card");
        if (!card) return;

        document.querySelectorAll(".barber-card").forEach(c => c.classList.remove("selected"));
        card.classList.add("selected");

        selectedBarber = card.dataset.id;
        barberPrice    = parseInt(card.dataset.price);

        document.getElementById("formBarber").value    = selectedBarber;
        document.getElementById("sumBarber").innerText = card.dataset.name;

        // reset service
        selectedServices = [];
        totalPrice       = 0;
        document.querySelectorAll(".service-card").forEach(c => c.classList.remove("selected"));
        updateSummary();

        document.getElementById("serviceSection").style.display  = "block";
        document.getElementById("summarySection").style.display  = "block";
    });

    // Service click
    document.querySelectorAll(".service-card").forEach(card => {
        card.onclick = () => {
            const id           = card.dataset.id;
            const type         = card.dataset.type;
            const servicePrice = parseInt(card.dataset.price || 0);

            if (selectedServices.includes(id)) {
                selectedServices = selectedServices.filter(s => s !== id);
                card.classList.remove("selected");
                totalPrice -= (type === 'haircut') ? barberPrice : servicePrice;
            } else {
                selectedServices.push(id);
                card.classList.add("selected");
                totalPrice += (type === 'haircut') ? barberPrice : servicePrice;
            }

            updateSummary();
        };
    });

    function updateSummary() {
        const grandTotal = totalPrice + ADMIN_FEE;

        document.getElementById("sumService").innerText    = selectedServices.length > 0
            ? selectedServices.length + " service dipilih"
            : "-";
        document.getElementById("sumSubtotal").innerText   = totalPrice.toLocaleString('id-ID');
        document.getElementById("sumGrandTotal").innerText = grandTotal.toLocaleString('id-ID');
        document.getElementById("formTotal").value         = grandTotal;

        // enable submit hanya jika semua terisi
        const ready = selectedDate && selectedTime && selectedBarber && selectedServices.length > 0;
        document.getElementById("submitBtn").disabled = !ready;
    }

    // Inject service_ids sebelum submit
    document.getElementById("bookingForm").addEventListener("submit", function() {
        this.querySelectorAll("input[name='service_ids[]']").forEach(i => i.remove());
        selectedServices.forEach(id => {
            const input  = document.createElement("input");
            input.type   = "hidden";
            input.name   = "service_ids[]";
            input.value  = id;
            this.appendChild(input);
        });
    });

    function resetFrom(level) {
        if (level === 'slot') {
            selectedTime = null;
            selectedBarber = null;
            selectedServices = [];
            totalPrice = 0;
            document.getElementById("barberSection").style.display  = "none";
            document.getElementById("serviceSection").style.display = "none";
            document.getElementById("summarySection").style.display = "none";
            document.getElementById("barberContainer").innerHTML    = "";
            document.querySelectorAll(".service-card").forEach(c => c.classList.remove("selected"));
            updateSummary();
        }
        if (level === 'barber') {
            selectedBarber = null;
            selectedServices = [];
            totalPrice = 0;
            document.getElementById("serviceSection").style.display = "none";
            document.getElementById("summarySection").style.display = "none";
            document.querySelectorAll(".barber-card").forEach(c => c.classList.remove("selected"));
            document.querySelectorAll(".service-card").forEach(c => c.classList.remove("selected"));
            updateSummary();
        }
    }
</script>
@endsection