<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Simply Haircut</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            scroll-behavior: smooth;
        }

        /* NAVBAR OFFSET */
        section {
            scroll-margin-top: 80px;
        }

        /* HERO */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, .35), rgba(0, 0, 0, .35)), url('https://simplyhaircut.id/storage/images/hero/carousel-2.png') center/cover no-repeat;
            position: relative;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .35);
        }

        /* SERVICE */
        .service-card {
            text-align: center;
        }

        .service-icon {
            width: 90px;
            height: 90px;
            background: #4b2e1e;
            color: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: auto;
        }

        /* ABOUT */
        .about {
            min-height: 100vh;
            background: url('https://simplyhaircut.id/storage/images/about/about.png') center/cover no-repeat;
            position: relative;
            color: #fff;
            display: flex;
            align-items: center;
        }

        /* CTA */
        .cta {
            min-height: 100vh;
            background: url('https://simplyhaircut.id/storage/images/book/book2.png') center/cover no-repeat;
            position: relative;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        footer {
            background: #4b2e1e;
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SIMPLY HAIRCUT</a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="nav" class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a href="#" class="nav-link">HOME</a></li>
                    <li class="nav-item"><a href="#service" class="nav-link">SERVICES</a></li>
                    <li class="nav-item"><a href="#about" class="nav-link">ABOUT US</a></li>
                    <li class="nav-item"><a href="#contact" class="nav-link">CONTACT US</a></li>



                    @auth
                        <li class="nav-item">
                            <a href="{{ url('/home') }}" class="btn btn-outline-dark btn-sm">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">
                                Login
                            </a>
                        </li>

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-dark btn-sm">
                                    Register
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero d-flex align-items-center">
        <div class="container position-relative text-white text-center">
            <h1 class="fw-bold display-4">Simply Haircut</h1>
            <p class="lead">Classic & Modern Barber Experience</p>
        </div>
    </section>

    <!-- SERVICE -->
    <section id="service" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="fw-bold">SERVICE</h3>
                <p class="text-muted">Choose the service you want and select the hair artist you prefer</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-droplet"></i></div>
                        <h6 class="mt-3">CREAMBATH</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-scissors"></i></div>
                        <h6 class="mt-3">HAIRCUT</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <h6 class="mt-3">PERM</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-person"></i></div>
                        <h6 class="mt-3">SHAVING</h6>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-palette"></i></div>
                        <h6 class="mt-3">COLORING</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-truck"></i></div>
                        <h6 class="mt-3">HOME DELIVERY</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-heart"></i></div>
                        <h6 class="mt-3">DISKON JUMAT BERKAH</h6>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi bi-grid"></i></div>
                        <h6 class="mt-3">OTHER SERVICES</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="about">
        <div class="container position-relative">
            <div class="row">
                <div class="col-md-7">
                    <h3 class="fw-bold mb-3">ABOUT US</h3>
                    <p>
                        Simply Haircut merupakan salah satu barbershop yang memiliki ciri khas
                        yang belum tentu dimiliki barbershop lain pada umumnya.
                    </p>
                    <p>
                        Simply Haircut mengusung tema simple dengan perpaduan classic
                        dan modern yang bertujuan memberikan rasa nyaman serta ketenangan
                        kepada setiap customer yang datang.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta text-center">
        <div class="container position-relative">
            <h2 class="fw-bold mb-3">Want to cut your hair?</h2>
            @auth
                <a href="{{ route('booking.create') }}" class="btn btn-success px-4">
                    Book Now →
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-success px-4">
                    Book Now →
                </a>
            @endauth

        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contact" class="pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h6 class="fw-bold">Contact Us</h6>
                    <p class="mb-1">Monday - Sunday: 09.30 AM - 10.00 PM</p>
                    <p class="mb-1">Friday: 02.00 PM - 10.00 PM</p>
                    <p class="mb-1">Phone: 085169765567</p>
                    <p>Email: simplycompany22@gmail.com</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Location</h6>
                    <p>
                        Yogyakarta (Kasihan Area)<br>
                        Rukoman Jl. Sunan Kudus No.5A,<br>
                        Kasihan, Bantul, Yogyakarta 55184
                    </p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Useful Links</h6>
                    <ul class="list-unstyled">
                        <li>Home</li>
                        <li>Academy</li>
                        <li>Book</li>
                        <li>Services</li>
                        <li>About Us</li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-4 small">
                © 2025 Simply Haircut
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
