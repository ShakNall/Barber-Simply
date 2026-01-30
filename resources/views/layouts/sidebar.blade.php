<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
    <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
        <i class="fe fe-x"></i>
    </a>

    <nav class="vertnav navbar navbar-light">

        {{-- BRAND --}}
        <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="/">
                <img src="https://simplyhaircut.id/storage/images/navbar/simplywith-outline.svg" width="40" alt="">
                Simply
                Haircut
            </a>
        </div>

        {{-- DASHBOARD (Semua role punya) --}}
        <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
                <a class="nav-link {{ Request::is('home') ? 'text-primary fw-bold' : '' }}" href="/home">
                    <i class="fe fe-home fe-16"></i>
                    <span class="ml-3 item-text">Dashboard</span>
                </a>
            </li>
        </ul>

        {{-- ======================= CUSTOMER MENU ======================= --}}
        @if (Auth::user()->role == 'customer')
            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Booking</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('booking/create') ? 'text-primary fw-bold' : '' }}"
                        href="/booking/create">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Buat Booking</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('booking*') ? 'text-primary fw-bold' : '' }}" href="/booking/history">
                        <i class="fe fe-list fe-16"></i>
                        <span class="ml-3 item-text">Riwayat Booking</span>
                    </a>
                </li>
            </ul>

            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Akun</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('profile') ? 'text-primary fw-bold' : '' }}" href="/profile">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Profil</span>
                    </a>
                </li>
            </ul>
        @endif



        {{-- ======================= BARBER MENU ======================= --}}
        @if (Auth::user()->role == 'barber')
            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Booking Saya</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/admin/bookings') ? 'text-primary fw-bold' : '' }}"
                        href="/admin/bookings">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Manajemen Layanan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('barber/shifts') ? 'text-primary fw-bold' : '' }}"
                        href="/barber/shifts">
                        <i class="fe fe-clock fe-16"></i>
                        <span class="ml-3 item-text">Shift Saya</span>
                    </a>
                </li>

            </ul>

            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Akun</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('profile') ? 'text-primary fw-bold' : '' }}" href="/profile">
                        <i class="fe fe-user fe-16"></i>
                        <span class="ml-3 item-text">Profil</span>
                    </a>
                </li>
            </ul>
        @endif



        {{-- ======================= ADMIN MENU ======================= --}}
        @if (Auth::user()->role == 'admin')
            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Master Data</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('barbers*') ? 'text-primary fw-bold' : '' }}" href="/barbers">
                        <i class="fe fe-users fe-16"></i>
                        <span class="ml-3 item-text">Kapster</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('services*') ? 'text-primary fw-bold' : '' }}" href="/services">
                        <i class="fe fe-scissors fe-16"></i>
                        <span class="ml-3 item-text">Services</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('shifts*') ? 'text-primary fw-bold' : '' }}" href="/shifts">
                        <i class="fe fe-clock fe-16"></i>
                        <span class="ml-3 item-text">Shifts</span>
                    </a>
                </li>
            </ul>

            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Operasional</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/bookings*') ? 'text-primary fw-bold' : '' }}"
                        href="/admin/bookings">
                        <i class="fe fe-calendar fe-16"></i>
                        <span class="ml-3 item-text">Manajemen Pemesanan</span>
                    </a>
                </li>
            </ul>

            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Manajemen User</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('profile*') ? 'text-primary fw-bold' : '' }}" href="/profile">
                        <i class="fe fe-user-plus fe-16"></i>
                        <span class="ml-3 item-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users*') ? 'text-primary fw-bold' : '' }}" href="/users">
                        <i class="fe fe-user-plus fe-16"></i>
                        <span class="ml-3 item-text">Users</span>
                    </a>
                </li>
            </ul>

            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Monitoring</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('reports*') ? 'text-primary fw-bold' : '' }}" href="/reports">
                        <i class="fe fe-pie-chart fe-16"></i>
                        <span class="ml-3 item-text">Laporan</span>
                    </a>
                </li>

            </ul>
        @endif



        {{-- ======================= OWNER MENU ======================= --}}
        @if (Auth::user()->role == 'owner')
            <p class="text-muted nav-heading mt-4 mb-1">
                <span>Monitoring</span>
            </p>

            <ul class="navbar-nav flex-fill w-100 mb-2">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('reports*') ? 'text-primary fw-bold' : '' }}" href="/reports">
                        <i class="fe fe-pie-chart fe-16"></i>
                        <span class="ml-3 item-text">Laporan</span>
                    </a>
                </li>

            </ul>
        @endif
    </nav>
</aside>