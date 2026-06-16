<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SurveySwap</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            min-height: 100vh;
            background: #fff;
        }

        /* ── Left panel ── */
        .left-panel {
            width: 55%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
        }

        .brand {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .brand span:first-child { color: #1a1a1a; }
        .brand span:last-child  { color: #e8613a; }

        .subtitle {
            color: #6b6b6b;
            font-size: 0.875rem;
            margin-bottom: 40px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            max-width: 370px;
            padding: 11px 14px;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #1a1a1a;
            outline: none;
            transition: border-color .2s;
        }
        input:focus { border-color: #b07a5a; }
        input::placeholder { color: #aaa; }

        .row-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 370px;
            margin-bottom: 24px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
        }
        .remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #b07a5a;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1a1a1a;
            text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-primary {
            width: 100%;
            max-width: 370px;
            padding: 12px;
            background: #b07a5a;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s;
            margin-bottom: 12px;
        }
        .btn-primary:hover { background: #9a6849; }

        .btn-google {
            width: 100%;
            max-width: 370px;
            padding: 11px;
            background: #fff;
            color: #1a1a1a;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1.5px solid #d0d0d0;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: background .2s, border-color .2s;
            margin-bottom: 28px;
        }
        .btn-google:hover { background: #f5f5f5; border-color: #bbb; }

        .google-icon {
            width: 20px;
            height: 20px;
        }

        .signup-text {
            font-size: 0.8rem;
            color: #6b6b6b;
            text-align: center;
            max-width: 370px;
        }
        .signup-text a {
            color: #e8613a;
            font-weight: 600;
            text-decoration: none;
        }
        .signup-text a:hover { text-decoration: underline; }

        /* Alerts */
        .alert-error {
            max-width: 370px;
            padding: 10px 14px;
            background: #fff0f0;
            border: 1px solid #f5c6c6;
            border-radius: 8px;
            color: #c0392b;
            font-size: 0.8rem;
            margin-bottom: 16px;
        }
        .alert-success {
            max-width: 370px;
            padding: 10px 14px;
            background: #f0fff4;
            border: 1px solid #b7ebc8;
            border-radius: 8px;
            color: #1e8c4a;
            font-size: 0.8rem;
            margin-bottom: 16px;
        }

        /* ── Right panel ── */
        .right-panel {
            width: 45%;
            background: #5c2d0a url('/images/surveyswap-bg.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Fallback gradient if image not present */
        .right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 40% 40%, #7a3c12 0%, #3d1a06 100%);
            z-index: 0;
        }
        .right-panel img { position: relative; z-index: 1; width: 75%; max-width: 380px; }

        @media (max-width: 768px) {
            .left-panel { width: 100%; padding: 40px 24px; }
            .right-panel { display: none; }
        }
    </style>
</head>
<body>

<!-- ── Left: Login form ── -->
<div class="left-panel">
    <div class="brand">
        <span>SURVEY</span><span>SWAP</span>
    </div>
    <p class="subtitle">Welcome back! Please enter your details.</p>

    {{-- Session flash messages --}}
    @if (session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                value="{{ old('username') }}"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <div class="row-inline">
            <label class="remember">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password</a>
        </div>

        <button type="submit" class="btn-primary">Sign in</button>
    </form>

    <a href="{{ route('auth.google') }}" class="btn-google">
        {{-- Inline Google "G" SVG --}}
        <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Sign in with Google
    </a>

    <p class="signup-text">
        Don't have an account?
        <a href="{{ route('register') }}">Sign up to free!</a>
    </p>
</div>

<!-- ── Right: Branding panel ── -->
<div class="right-panel">
    {{-- Ganti src dengan path logo SurveySwap kamu --}}
    <img src="{{ asset('images/surveyswap-logo.png') }}" alt="SurveySwap">
</div>

</body>
</html>
