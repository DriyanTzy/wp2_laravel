@extends('layouts.app')

@section('title', 'LogOut')

@section('content')
<style>
    .logout-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }
    .logout-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        padding: 40px 48px;
        max-width: 480px;
        width: 100%;
        text-align: center;
    }
    .logout-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #e5e7eb;
        margin: 0 auto 16px;
        display: block;
    }
    .logout-email {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }
    .logout-message {
        font-size: 15px;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 28px;
    }
    .logout-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .btn-logout-danger {
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 14px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.2s;
        width: 100%;
    }
    .btn-logout-danger:hover {
        background: #b91c1c;
    }
    .btn-logout-danger:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .btn-logout-cancel {
        background: transparent;
        color: #374151;
        border: 1.5px solid #d1d5db;
        border-radius: 50px;
        padding: 14px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: 0.2s;
        width: 100%;
        text-decoration: none;
        display: inline-block;
    }
    .btn-logout-cancel:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }
    .logout-footer {
        margin-top: 24px;
        font-size: 13px;
        color: #9ca3af;
    }
    .loading-spinner-logout {
        display: none;
        margin: 0 auto;
        width: 24px;
        height: 24px;
        border: 3px solid #f3f4f6;
        border-top-color: #dc2626;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .spinner-text {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @media (max-width: 500px) {
        .logout-card {
            padding: 24px 20px;
        }
    }
</style>

<div class="logout-wrapper">
    <div class="logout-card">
        <img id="logoutAvatar" src="" alt="Avatar" class="logout-avatar">
        <div class="logout-email" id="logoutEmail">Memuat...</div>
        <div class="logout-message">
            Yakin ingin keluar dari sesi ini?<br>
            Kamu perlu login kembali untuk mengakses akunmu.
        </div>

        <div class="logout-actions">
            <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout-danger" id="logoutBtn">
                    <span class="spinner-text">
                        <span id="btnText">Keluar dari akun</span>
                        <span class="loading-spinner-logout" id="spinner"></span>
                    </span>
                </button>
            </form>
            <a href="{{ url()->previous() ?? route('dashboard') }}" class="btn-logout-cancel">Batal</a>
        </div>

        <div class="logout-footer">
            {{ now()->format('d F Y') }}
        </div>
    </div>
</div>

<script>
    // Ambil data user dari sessionStorage atau fetch /me-data
    (async function loadUser() {
        try {
            // Coba ambil dari sessionStorage dulu (disimpan oleh layout)
            let userData = sessionStorage.getItem('ss_user');
            if (userData) {
                userData = JSON.parse(userData);
                setUser(userData);
                return;
            }

            // Kalau tidak ada, fetch
            const res = await fetch('{{ url("/me-data") }}', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Gagal memuat data user');
            const data = await res.json();
            const user = data.user;
            sessionStorage.setItem('ss_user', JSON.stringify(user));
            setUser(user);
        } catch (err) {
            console.warn('Gagal ambil user:', err);
            document.getElementById('logoutEmail').textContent = 'antonauditore88@gmail.com'; // fallback
            document.getElementById('logoutAvatar').src = 'https://ui-avatars.com/api/?name=Anton&background=444&color=fff';
        }
    })();

    function setUser(user) {
        document.getElementById('logoutEmail').textContent = user.email || 'email@example.com';
        const avatarSrc = user.photo
            ? '/storage/' + user.photo
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'User')}&background=444&color=fff&size=80`;
        document.getElementById('logoutAvatar').src = avatarSrc;
        document.getElementById('logoutAvatar').onerror = function() {
            this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'User')}&background=444&color=fff&size=80`;
        };
    }

    // Event submit untuk menampilkan spinner
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('logoutBtn');
        btn.disabled = true;
        document.getElementById('btnText').textContent = 'Keluar...';
        document.getElementById('spinner').style.display = 'inline-block';
    });
</script>
@endsection