<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Password</title>

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
            object-fit: contain;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="auth-box text-center px-3">

        <img src="/logo.png" class="logo mb-3" alt="Logo">

        <h4 class="fw-bold">Confirm Password</h4>
        <p class="text-muted">
            Please confirm your password before continuing.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    placeholder="Enter your password" 
                    required autocomplete="current-password" autofocus>

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-black">
                Confirm Password
            </button>
        </form>

        @if (Route::has('password.request'))
            <p class="mt-4">
                <a href="{{ route('password.request') }}" class="text-muted small">
                    Forgot Your Password?
                </a>
            </p>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>