@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* ===========================
                                                                                                                                                                               WIZARD PROGRESS
                                                                                                                                                                            =========================== */
        .wizard-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .wizard-step {
            flex: 1;
            text-align: center;
            font-weight: 600;
            color: #aaa;
            position: relative;
        }

        .wizard-step.active {
            color: #0d6efd;
        }

        .wizard-step.completed {
            color: #198754;
        }

        .wizard-step::before {
            content: attr(data-step);
            width: 34px;
            height: 34px;
            line-height: 34px;
            display: inline-block;
            background: #ddd;
            color: white;
            border-radius: 50%;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .wizard-step.active::before {
            background: #007bff;
        }

        .wizard-step.completed::before {
            background: #28a745;
        }

        .wizard-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 17px;
            left: 55%;
            width: 90%;
            height: 3px;
            background: #ddd;
            z-index: -1;
        }

        .wizard-step.completed:not(:last-child)::after {
            background: #28a745;
        }

        /* ---------------------------
                                                                                                                                                                                    PAGE ANIMATION SLIDE
                                                                                                                                                                                ---------------------------- */
        .step-page {
            display: none;
            animation-duration: 0.4s;
        }

        .step-page.active {
            display: block;
        }

        .slide-left {
            animation-name: slideLeft;
        }

        .slide-right {
            animation-name: slideRight;
        }

        @keyframes slideLeft {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ===========================
                                                                                                                                                                               CARD STYLE
                                                                                                                                                                            =========================== */
        .barber-card,
        .service-card {
            border-radius: 12px;
            border: 1px solid #eee;
            padding: 10px;
            cursor: pointer;
            transition: .2s;
        }

        .barber-card:hover,
        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, .08);
        }

        .selected {
            border: 2px solid #0d6efd;
        }

        /* SLOT */
        .slot-btn {
            min-width: 90px;
            margin: 5px;
            border-radius: 10px;
        }

        .slot-btn.active {
            background: #198754;
            color: #fff;
        }
    </style>

    <div class="container mt-4">

        {{-- PROGRESS --}}
        <div class="wizard-container">
            <div class="wizard-step active" id="nav1">Tanggal</div>
            <div class="wizard-step" id="nav2">Kapster</div>
            <div class="wizard-step" id="nav3">Service</div>
            <div class="wizard-step" id="nav4">Jam</div>
            <div class="wizard-step" id="nav5">Konfirmasi</div>
        </div>

        {{-- STEP 1 : TANGGAL --}}
        <div id="step1" class="step-page active slide-right">
            <h4>Pilih Tanggal</h4>
            <input type="text" id="date" class="form-control col-md-4 mb-3">
            <button class="btn btn-primary" id="nextToBarber">Lanjut</button>
        </div>

        {{-- STEP 2 : BARBER --}}
        <div id="step2" class="step-page">
            <button class="btn btn-outline-secondary my-3" onclick="goBack(1)">
                ← Kembali
            </button>
            <h3>Pilih Kapster</h3>
            <p class="text-muted mb-3">Pilih kapster yang tersedia.</p>

            <div class="row" id="barberContainer">
                {{-- diisi via JS --}}
            </div>
        </div>


        {{-- STEP 3 : SERVICE --}}
        <div id="step3" class="step-page">
            <button class="btn btn-outline-secondary my-3" onclick="goBack(2)">
                ← Kembali
            </button>
            <h3>Pilih Service</h3>
            <p class="text-muted mb-3">Anda bisa memilih lebih dari satu layanan.</p>

            <div class="row">
                @foreach ($services as $s)
                    <div class="col-md-4 mb-3">
                        <div class="service-card p-2 shadow-sm" data-id="{{ $s->id }}"
                            data-name="{{ $s->name }}" data-price="{{ $s->price }}"
                            data-duration="{{ $s->duration }}"
                            data-type="{{ strtolower($s->name) === 'haircut' ? 'haircut' : 'normal' }}">

                            <img src="{{ asset('storage/' . ($s->image ?? 'default-service.jpg')) }}"
                                class="w-100 rounded mb-2" style="height:160px;object-fit:cover;">

                            <h5 class="text-center">{{ $s->name }}</h5>
                            <p class="text-muted text-center mb-1">
                                {{ $s->duration }} menit
                            </p>
                            <p class="text-center text-muted">
                                @if (strtolower($s->name) === 'haircut')
                                    Mengikuti harga kapster
                                @else
                                    Rp {{ number_format($s->price) }}
                                @endif
                            </p>

                        </div>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary mt-3" id="nextToSlot">
                Lanjut
            </button>
        </div>


        {{-- STEP 4 : JAM --}}
        <div id="step4" class="step-page" style="display:none">
            <button class="btn btn-outline-secondary my-3" onclick="goBack(3)">
                ← Kembali
            </button>
            <h3>Pilih Jam</h3>

            <div id="slotContainer" class="d-flex flex-wrap"></div>
        </div>

        {{-- STEP 5 : KONFIRMASI --}}
        <div id="step5" class="step-page" style="display:none">

            <button type="button" class="btn btn-outline-secondary my-3" onclick="goBack(4)">
                ← Kembali
            </button>
            <h4>Konfirmasi Booking</h4>

            <div class="card p-3">
                <p><b>Tanggal:</b> <span id="sumDate"></span></p>
                <p><b>Kapster:</b> <span id="sumBarber"></span></p>
                <p><b>Service:</b> <span id="sumService"></span></p>
                <p><b>Jam:</b> <span id="sumTime"></span></p>

                <hr>

                <p class="d-flex justify-content-between mb-1">
                    <span>Subtotal Service</span>
                    <span>Rp <span id="sumSubtotal"></span></span>
                </p>

                <p class="d-flex justify-content-between mb-1">
                    <span>Biaya Admin</span>
                    <span>Rp 5.000</span>
                </p>

                <hr>

                <h4 class="text-primary d-flex justify-content-between">
                    <span>Total Bayar</span>
                    <span>Rp <span id="sumGrandTotal"></span></span>
                </h4>

                <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                    @csrf
                    <input type="hidden" name="date" id="formDate">
                    <input type="hidden" name="barber_id" id="formBarber">
                    <input type="hidden" name="time" id="formTime">
                    <input type="hidden" name="admin_fee" value="5000">
                    <input type="hidden" name="total_price" id="formTotal">

                    <button class="btn btn-success w-100 mt-3">
                        Konfirmasi Booking
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const ADMIN_FEE = 5000;
        let grandTotal = 0;
        let selectedDate = null;
        let selectedBarber = null;
        let selectedServices = [];
        let totalPrice = 0;

        /* =====================
           DATE
        ===================== */
        flatpickr("#date", {
            minDate: "today",
            dateFormat: "Y-m-d"
        });

        document.getElementById("nextToBarber").onclick = () => {
            selectedDate = document.getElementById("date").value;
            if (!selectedDate) return alert("Pilih tanggal dulu");

            document.getElementById("formDate").value = selectedDate;
            document.getElementById("sumDate").innerText = selectedDate;

            fetch("/booking/barbers", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        date: selectedDate
                    })
                })
                .then(r => r.json())
                .then(barbers => {
                    const container = document.getElementById("barberContainer");
                    container.innerHTML = "";

                    if (barbers.length === 0) {
                        container.innerHTML = "<p class='text-muted'>Tidak ada kapster tersedia</p>";
                        return;
                    }

                    barbers.forEach(b => {
                        container.innerHTML += `
        <div class="col-md-4 mb-3">
            <div class="barber-card p-2 shadow-sm"
                data-id="${b.id}"
                data-name="${b.user.name}"
                data-price="${b.price}">

                <img src="/storage/${b.image ?? 'default-barber.jpg'}"
                     class="w-100 rounded mb-2"
                     style="height:160px;object-fit:cover;">

                <h5 class="text-center">${b.user.name}</h5>
                <p class="text-center text-muted mb-1">
                    Rp ${Number(b.price).toLocaleString()}
                </p>

                <button class="btn btn-primary btn-sm w-100 mt-1 pilih-barber">
                    Pilih Kapster
                </button>
            </div>
        </div>
    `;
                    });


                    showStep(2);
                });
        };

        /* =====================
           BARBER
        ===================== */
        let barberPrice = 0;

        document.addEventListener("click", e => {
            const card = e.target.closest(".barber-card");
            if (!card) return;

            document.querySelectorAll(".barber-card")
                .forEach(c => c.classList.remove("selected"));

            card.classList.add("selected");

            selectedBarber = card.dataset.id;
            barberPrice = parseInt(card.dataset.price);

            document.getElementById("formBarber").value = selectedBarber;
            document.getElementById("sumBarber").innerText = card.dataset.name;

            selectedServices = [];
            totalPrice = 0;

            document.querySelectorAll(".service-card")
                .forEach(c => c.classList.remove("selected"));

            document.getElementById("sumService").innerText = "-";
            document.getElementById("sumSubtotal").innerText = "0";

            showStep(3);
        });


        /* =====================
           SERVICE
        ===================== */
        document.querySelectorAll(".service-card").forEach(card => {
            card.onclick = () => {
                const id = card.dataset.id;
                const type = card.dataset.type;
                const servicePrice = parseInt(card.dataset.price || 0);

                if (selectedServices.includes(id)) {
                    selectedServices = selectedServices.filter(s => s !== id);
                    card.classList.remove("selected");

                    if (type === 'haircut') {
                        totalPrice -= barberPrice;
                    } else {
                        totalPrice -= servicePrice;
                    }

                } else {
                    selectedServices.push(id);
                    card.classList.add("selected");

                    if (type === 'haircut') {
                        totalPrice += barberPrice;
                    } else {
                        totalPrice += servicePrice;
                    }
                }

                document.getElementById("sumService").innerText =
                    selectedServices.length + " service dipilih";

                document.getElementById("sumSubtotal").innerText =
                    totalPrice.toLocaleString();
            };
        });



        document.getElementById("nextToSlot").onclick = () => {
            if (selectedServices.length === 0) return alert("Pilih service dulu");

            const form = document.getElementById("bookingForm");

            form.querySelectorAll("input[name='service_ids[]']").forEach(i => i.remove());

            selectedServices.forEach(id => {
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "service_ids[]";
                input.value = id;
                form.appendChild(input);
            });

            fetch("/booking/slots", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        barber_id: selectedBarber,
                        service_ids: selectedServices,
                        date: selectedDate
                    })
                })
                .then(r => r.json())
                .then(res => {
                    const container = document.getElementById("slotContainer");
                    container.innerHTML = "";

                    const allSlots = res.allSlots ?? [];
                    const bookedSlots = res.bookedSlots ?? [];

                    allSlots.forEach(slot => {
                        let btn = document.createElement("button");
                        btn.type = "button";
                        btn.className = "slot-btn btn";
                        btn.innerText = slot;

                        if (bookedSlots.includes(slot)) {
                            // 🔒 SLOT BENTROK
                            btn.classList.add("btn-secondary");
                            btn.disabled = true;
                            btn.innerText = slot + " (Booked)";
                        } else {
                            // ✅ SLOT KOSONG
                            btn.classList.add("btn-outline-success");

                            btn.onclick = () => {
                                document.querySelectorAll(".slot-btn").forEach(b =>
                                    b.classList.remove("active")
                                );

                                btn.classList.add("active");

                                grandTotal = totalPrice + ADMIN_FEE;

                                document.getElementById("formTime").value = slot;
                                document.getElementById("sumTime").innerText = slot;

                                document.getElementById("sumSubtotal").innerText =
                                    totalPrice.toLocaleString();

                                document.getElementById("sumGrandTotal").innerText =
                                    grandTotal.toLocaleString();

                                document.getElementById("formTotal").value = grandTotal;

                                showStep(5);
                            };
                        }

                        container.appendChild(btn);
                    });

                    showStep(4);
                });

        };

        /* =====================
           HELPER
        ===================== */
        function showStep(n) {
            for (let i = 1; i <= 5; i++) {
                const stepEl = document.getElementById("step" + i);
                const navEl = document.getElementById("nav" + i);

                stepEl.style.display = i === n ? "block" : "none";

                navEl.classList.remove("active", "completed");

                if (i === n) navEl.classList.add("active");
                if (i < n) navEl.classList.add("completed");
            }
        }

        function goBack(step) {
            showStep(step);
        }
    </script>
@endsection
