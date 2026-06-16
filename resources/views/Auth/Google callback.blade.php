<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in with Google – SurveySwap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #f8f8f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .google-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
            width: 420px;
            overflow: hidden;
        }

        .google-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            border-bottom: 1px solid #e0e0e0;
        }
        .google-header svg { width: 20px; height: 20px; flex-shrink: 0; }
        .google-header span {
            font-size: 0.9rem;
            color: #444;
            font-weight: 500;
        }

        .google-body {
            padding: 32px 24px 28px;
            text-align: center;
        }

        /* ── Verifying state ── */
        .state-verifying { display: none; }
        .state-verifying.active { display: block; }
        .verifying-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
        }
        .verifying-sub {
            font-size: 0.85rem;
            color: #666;
        }

        /* ── Success state ── */
        .state-success { display: none; }
        .state-success.active { display: block; }

        .check-circle {
            width: 48px;
            height: 48px;
            background: #34a853;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }
        .check-circle svg { width: 26px; height: 26px; }

        .success-title {
            font-size: 0.9rem;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .success-sub {
            font-size: 0.82rem;
            color: #666;
            margin-bottom: 16px;
        }

        .progress-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 8px;
        }
        .progress-fill {
            height: 100%;
            background: #1a73e8;
            border-radius: 2px;
            width: 0%;
            animation: fillBar 2s ease forwards;
        }
        @keyframes fillBar { to { width: 100%; } }

        /* Footer */
        .footer {
            margin-top: 36px;
            display: flex;
            gap: 24px;
            font-size: 0.78rem;
            color: #666;
        }
        .footer a { color: #666; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .google-card { width: 90%; }
        }
    </style>
</head>
<body>

<div class="google-card">
    <div class="google-header">
        <!-- Google "G" icon -->
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        <span>Sign in with Google</span>
    </div>

    <div class="google-body">

        {{-- Verifying state --}}
        <div class="state-verifying" id="stateVerifying">
            <div class="verifying-title">Memverifikasi akun...</div>
            <div class="verifying-sub">Menghubungkan ke survei swap</div>
        </div>

        {{-- Success state --}}
        <div class="state-success" id="stateSuccess">
            <div class="check-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="success-title">Login berhasil!</div>
            <div class="success-sub">Mengalihkan ke SurveiSwap Dashboard...</div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

    </div>
</div>

<div class="footer">
    <span>English (United States) ▾</span>
    <a href="#">Help</a>
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
</div>

<script>
    // Show verifying first, then transition to success
    const verifying = document.getElementById('stateVerifying');
    const success   = document.getElementById('stateSuccess');

    verifying.classList.add('active');

    setTimeout(() => {
        verifying.classList.remove('active');
        success.classList.add('active');

        // After progress bar finishes, redirect to dashboard
        setTimeout(() => {
            window.location.href = "{{ route('dashboard') }}";
        }, 2200);
    }, 1500);
</script>

</body>
</html>