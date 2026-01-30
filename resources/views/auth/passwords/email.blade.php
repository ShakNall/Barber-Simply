<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff;
        }

        .auth-box {
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

    <div class="auth-box text-center">

        <img src="/logo.png" class="logo mb-3" alt="Logo">

        <h4 class="fw-bold">Forgot your password?</h4>
        <p class="text-muted">
            Enter your email address and we’ll send you a password reset link.
        </p>

        @if (session('status'))
            <div class="alert alert-success text-start">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email Address" value="{{ old('email') }}" required autofocus>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-black">
                Send Password Reset Link
            </button>
        </form>

        <p class="mt-4">
            Remember your password?
            <a href="{{ route('login') }}" class="fw-bold text-dark">
                Back to Login
            </a>
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
