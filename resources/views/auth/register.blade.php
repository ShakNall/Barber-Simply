<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff;
        }

        .register-box {
            max-width: 420px;
            margin: auto;
            padding-top: 50px;
        }

        .btn-black {
            background: #000;
            color: #fff;
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .btn-black:hover {
            background: #111;
        }

        .form-control {
            height: 48px;
            border-radius: 6px;
        }

        .logo {
            width: 200px;
            height: 200px;
        }

        a {
            text-decoration: none;
        }

        label {
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="register-box text-center">

        <img src="/logo.png" class="logo" alt="Logo">
        <h4 class="fw-bold">Sign Up</h4>
        <p class="text-muted">Enter your details and create account</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3 text-start">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email Address" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Password Confirmation" required>
            </div>

            <div class="mb-3 text-start">
                <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
            </div>

            <div class="mb-4 text-start">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Agree with <strong>Terms And Conditions</strong>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-black">Sign Up</button>

        </form>

        <p class="mt-4">
            Already have an account?
            <a href="{{ route('login') }}" class="fw-bold">Login in here</a>
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
