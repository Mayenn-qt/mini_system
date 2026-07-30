<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Ohaiyo Japan Surplus</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #C8102E;
            --bg-color: #FFFFFF;
            --sec-bg-color: #F5F5F5;
            --border-color: #D9D9D9;
            --text-color: #4A4A4A;
            --accent-color: #1E1E1E;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--sec-bg-color);
            color: var(--text-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            background-color: var(--bg-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 420px;
            width: 100%;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--primary-color);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .japanese-sun {
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            border-radius: 50%;
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(200, 16, 46, 0.3);
        }

        .brand-name {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 20px;
            letter-spacing: 1px;
            margin: 0;
        }

        .brand-sub {
            font-size: 12px;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #a00d24;
            border-color: #a00d24;
        }

        .alert {
            font-size: 13px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <div class="japanese-sun">お</div>
        <h1 class="brand-name">OHAIYO JAPAN</h1>
        <span class="brand-sub">Surplus Management</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@ohaiyo.com">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
        </div>

        <div class="mb-3 form-check d-flex justify-content-between align-items-center">
            <div>
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" style="font-size:13px;" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Sign In</button>
    </form>
</div>

</body>
</html>
