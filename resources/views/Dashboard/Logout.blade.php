@extends('layouts.app')

@section('title', 'LogOut')

@section('content')

<h1 class="page-title">LogOut</h1>
<p  class="page-date" id="pageDate"></p>

<div class="logout-card">

    <div class="logout-avatar-wrap" id="logoutAvatar">
        {{-- Diisi JS dari sessionStorage / API --}}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            <circle cx="12" cy="12" r="11" stroke-width="1.5"/>
        </svg>
        <span class="online-dot"></span>
    </div>

    <p class="logout-email" id="logoutEmail">–</p>
    <p class="logout-msg">
        Yakin ingin keluar dari sesi ini? Kamu perlu login<br>
        kembali untuk mengakses akunmu.
    </p>

    {{-- POST ke route web (bukan API) supaya session + cookie dihapus --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Keluar dari akun
        </button>
    </form>

    <a href="{{ route('dashboard') }}" class="btn-batal">Batal</a>

</div>

@endsection

@push('scripts')
<script>
document.getElementById('pageDate').textContent =
    new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

// Ambil email dari sessionStorage (sudah diset sidebar)
const cached = sessionStorage.getItem('ss_user');
if (cached) {
    const u = JSON.parse(cached);
    document.getElementById('logoutEmail').textContent = u.email ?? '';

    // Ganti ikon SVG dengan foto profil kalau ada
    if (u.photo) {
        const wrap = document.getElementById('logoutAvatar');
        const dot  = wrap.querySelector('.online-dot');
        const img  = document.createElement('img');
        img.src    = u.photo;
        img.alt    = 'avatar';
        img.style  = 'width:80px;height:80px;border-radius:50%;object-fit:cover;';
        wrap.innerHTML = '';
        wrap.appendChild(img);
        wrap.appendChild(dot);
    }
} else {
    // Fallback: fetch sekali kalau cache kosong
    fetch('/api/profile', {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(r => r.ok ? r.json() : null)
    .then(data => {
        if (!data) return;
        document.getElementById('logoutEmail').textContent = data.user?.email ?? '';
    });
}
</script>
@endpush