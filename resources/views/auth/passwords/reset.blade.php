<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>

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

        <h4 class="fw-bold">Reset Password</h4>
        <p class="text-muted">
            Please enter your new password below.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3 text-start">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ $email ?? old('email') }}" required autocomplete="email" readonly>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter new password" required autocomplete="new-password" autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 text-start">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Repeat new password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-black">
                Reset Password
            </button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>