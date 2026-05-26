<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WKAS Backoffice</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        body {
            background-color: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .login-card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo {
            width: 48px;
            height: 48px;
            background-color: var(--primary);
            color: #fff;
            border-radius: var(--radius);
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .login-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }
        .login-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-accent-border"></div>
        <div class="login-header">
            <div class="login-logo">W</div>
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Silakan login untuk masuk ke backoffice WKAS</p>
        </div>

        @if ($errors->any())
            <div style="background-color: var(--danger-light); color: var(--danger); padding: 0.75rem; border-radius: var(--radius); margin-bottom: 1.25rem; font-size: 0.825rem; border: 1px solid rgba(239, 68, 68, 0.15);">
                <ul style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="admin@wkas.com" required autofocus autocomplete="username">
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <!-- Remember Me & Forgot Password -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.85rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary);">
                    <input type="checkbox" name="remember" style="cursor: pointer; width: 15px; height: 15px; accent-color: var(--primary);">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem; font-size: 0.95rem; font-weight: 600;">
                Masuk Ke Aplikasi
            </button>
        </form>
    </div>
</body>
</html>
