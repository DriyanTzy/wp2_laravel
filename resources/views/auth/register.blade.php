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
    <div class="brand">
    <span>SURVEY</span><span>SWAP</span>
</div>

<p class="subtitle">
    Welcome back! Please enter your details.
</p>

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
    <img src="{{ asset('images/gambar1.png') }}" alt="SurveySwap">
</div>

</body>
</html>
