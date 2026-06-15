<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – SurveySwap</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>

<div class="left-panel">

    <a href="{{ route('login') }}" class="back-arrow">&#8592;</a>

    <div class="brand">
        <span class="survey">SURVEY</span><span class="swap">SWAP</span> <span class="sign-up">SIGN UP</span>
    </div>
    <p class="subtitle">Welcome back! Please enter your details.</p>

    @if ($errors->any())
        <div class="alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                value="{{ old('name') }}"
                required
                autofocus
            >
        </div>

        {{-- Username — wajib karena RegisterRequest mensyaratkan username --}}
        <div class="form-group">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Example: driyan90 (huruf, angka, - _)"
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

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                required
            >
            <p class="hint">use 8 or more characters with a mix of latters, numbers &amp; symbols</p>
        </div>

        <p class="terms-text">
            By creating an account, you agree to<br>
            our <a href="#">terms of use</a> and <a href="#">Privacy Policy</a>
        </p>

        <button type="submit" class="btn-primary">Create an account</button>
    </form>
</div>

<div class="right-panel">
    <img src="{{ asset('images/gambar1.png') }}" alt="SurveySwap">
</div>

</body>
</html>
