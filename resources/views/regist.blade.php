<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SurveySwap Sign Up</title>
    <link rel="stylesheet" href="{{ asset('css/regist.css') }}">
</head>
<body>

<div class="container">

    <!-- Left Side -->
    <div class="form-section">

        <a href="#" class="back-btn">←</a>

        <h1>
            SURVEY<span>SWAP</span> SIGN UP
        </h1>

        <p class="subtitle">
            Welcome back! Please enter your details.
        </p>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your Email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password">
                <small>
                    use 8 or more characters with a mix of letters,
                    numbers & symbols
                </small>
            </div>

            <p class="terms">
                By creating an account, you agree to our
                <a href="#">terms of use</a> and
                <a href="#">Privacy Policy</a>
            </p>

            <button type="submit">
                Create an account
            </button>

        </form>
    </div>

    <!-- Right Side -->
    <div class="image-section">
        <img src="{{ asset('images/surveyswap.png') }}" alt="SurveySwap">
    </div>

</div>

</body>
</html>
