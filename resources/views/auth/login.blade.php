<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff;
        }

        .login-box {
            max-width: 420px;
            margin: auto;
            padding-top: 60px;
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
            color: #fff;
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
    </style>
</head>

<body>

    <div class="login-box text-center">

        <img src="/logo.png" class="logo" alt="Logo">

        <h4 class="fw-bold">Hi, Welcome Back</h4>
        <p class="text-muted">Enter your credentials to continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    placeholder="Email Address" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                    placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember">
                    <label class="form-check-label">Remember Me</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-dark fw-semibold">Forgot Password?</a>
                @endif
            </div>

            <button type="submit" class="btn btn-black">Sign In</button>

        </form>

        <p class="mt-4">
            Don't have an account?
            <a href="{{ route('register') }}" class="fw-bold">Create an account</a>
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
