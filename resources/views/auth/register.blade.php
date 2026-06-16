<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – SurveySwap</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 1.1rem;
            color: #1a1a1a;
            text-decoration: none;
            margin-bottom: 36px;
            width: fit-content;
        }
        .back-link:hover { color: #b07a5a; }

        .form-group { margin-bottom: 18px; }

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
            background: #fafafa;
        }
        input:focus { border-color: #b07a5a; background: #fff; }
        input.is-invalid { border-color: #e53e3e; }

        /* inline field error */
        .field-error {
            font-size: 0.75rem;
            color: #c0392b;
            margin-top: 4px;
            max-width: 370px;
        }

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
            margin-top: 8px;
        }
        .btn-primary:hover { background: #9a6849; }

        /* Right panel */
        .right-panel {
            width: 45%;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .right-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 40% 40%, #7a3c12 0%, #3d1a06 100%);
            z-index: 0;
        }
        .right-panel img { position: relative; z-index: 1; width: 75%; max-width: 380px; }

        @media (max-width: 768px) {
            .left-panel  { width: 100%; padding: 40px 24px; }
            .right-panel { display: none; }
        }
    </style>
</head>
<body>

<div class="left-panel">

    <a href="{{ route('login') }}" class="back-link">&#8592;</a>

    <form method="POST" action="/register">
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                value="{{ old('name') }}"
                class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                required
                autofocus
            >
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Username — wajib karena RegisterRequest mensyaratkan username --}}
        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Contoh: driyan90 (huruf, angka, - _)"
                value="{{ old('username') }}"
                class="{{ $errors->has('username') ? 'is-invalid' : '' }}"
                required
            >
            @error('username')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email address"
                value="{{ old('email') }}"
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Min. 8 karakter, huruf & angka"
                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                required
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Create an account</button>
    </form>
</div>

<div class="right-panel">
    <img src="{{ asset('images/surveyswap-logo.png') }}" alt="SurveySwap">
</div>

</body>
</html>
