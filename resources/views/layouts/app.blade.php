<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SurveySwap')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f0f0;
            display: flex;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background: #111;
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-user {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 44px;
        }

        .avatar-wrap {
            position: relative;
            width: 80px;
            height: 80px;
            margin-bottom: 12px;
        }
        .avatar-wrap img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            background: #333;
        }
        .avatar-wrap .notif-dot {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 16px;
            height: 16px;
            background: #e53e3e;
            border-radius: 50%;
            border: 2px solid #111;
        }

        /* Skeleton shimmer */
        .skeleton {
            background: linear-gradient(90deg, #222 25%, #333 50%, #222 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 6px;
        }
        @keyframes shimmer { to { background-position: -200% 0; } }
        .sk-avatar { width: 80px; height: 80px; border-radius: 12px; }
        .sk-name   { width: 100px; height: 14px; margin: 0 auto 6px; }
        .sk-email  { width: 140px; height: 11px; margin: 0 auto; }

        .sidebar-name  { color: #fff; font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; text-align: center; }
        .sidebar-email { color: #888; font-size: 0.75rem; text-align: center; }

        /* Nav */
        .sidebar-nav { display: flex; flex-direction: column; gap: 4px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            text-decoration: none;
            color: #aaa;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background .2s, color .2s;
        }
        .nav-link:hover  { background: #222; color: #fff; }
        .nav-link.active { background: #fff; color: #111; }
        .nav-link svg    { width: 18px; height: 18px; flex-shrink: 0; }

        /* ═══════════════════════════════════════
           MAIN
        ═══════════════════════════════════════ */
        .main { flex: 1; padding: 36px 40px; overflow-y: auto; }

        .page-title { font-size: 1.75rem; font-weight: 800; color: #111; }
        .page-date  { font-size: 0.85rem; color: #999; margin-top: 4px; margin-bottom: 28px; }

        /* ── Stat cards ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 36px;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
        }
        .stat-icon { margin-bottom: 12px; color: #333; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-label { font-size: 0.8rem; color: #777; margin-bottom: 6px; }
        .stat-value { font-size: 1.9rem; font-weight: 800; color: #111; margin-bottom: 8px; }
        .stat-trend {
            font-size: 0.78rem;
            color: #22c55e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .stat-trend svg { width: 12px; height: 12px; }

        /* ── Section ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .section-title { font-size: 1.2rem; font-weight: 800; color: #111; }
        .section-link  { font-size: 0.8rem; color: #3b82f6; text-decoration: none; }
        .section-link:hover { text-decoration: underline; }

        /* ── Survey / category list ── */
        .item-list { display: flex; flex-direction: column; gap: 10px; }
        .item-card {
            background: #fff;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .item-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            background: #f3f4f6;
        }
        .item-info { flex: 1; }
        .item-name { font-size: 0.9rem; font-weight: 600; color: #111; }
        .item-meta { font-size: 0.75rem; color: #999; margin-top: 2px; }
        .badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #e5e7eb; color: #374151; }

        /* ── Profile ── */
        .profile-header-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 36px;
        }
        .profile-avatar-wrap { position: relative; width: 64px; height: 64px; }
        .profile-avatar-wrap img {
            width: 64px; height: 64px;
            border-radius: 50%; object-fit: cover;
        }
        .profile-edit-badge {
            position: absolute;
            bottom: -2px; right: -2px;
            width: 22px; height: 22px;
            background: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 1px 4px rgba(0,0,0,.2);
            cursor: pointer;
        }
        .profile-edit-badge svg { width: 11px; height: 11px; color: #555; }
        .profile-hinfo .p-name  { font-size: 1rem; font-weight: 700; color: #111; }
        .profile-hinfo .p-email { font-size: 0.82rem; color: #777; margin-top: 2px; }
        .btn-edit {
            margin-left: auto;
            padding: 8px 22px;
            background: #3b82f6; color: #fff;
            font-size: 0.85rem; font-weight: 600;
            border: none; border-radius: 8px;
            cursor: pointer; text-decoration: none;
            transition: background .2s;
        }
        .btn-edit:hover { background: #2563eb; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 32px;
        }
        .form-field label {
            display: block;
            font-size: 0.82rem; font-weight: 600;
            color: #374151; margin-bottom: 8px;
        }
        .form-field input,
        .form-field select {
            width: 100%;
            padding: 12px 14px;
            background: #f3f4f6;
            border: none; border-radius: 8px;
            font-size: 0.875rem; color: #374151;
            outline: none;
            appearance: none; -webkit-appearance: none;
            transition: background .2s;
        }
        .form-field input:focus,
        .form-field select:focus { background: #e9ecf0; }
        .form-field input[readonly] { cursor: default; opacity: .7; }

        .select-wrap { position: relative; }
        .select-wrap::after {
            content: '';
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-top-color: #9ca3af;
            pointer-events: none;
        }

        .btn-save {
            padding: 10px 28px;
            background: #3b82f6; color: #fff;
            font-size: 0.875rem; font-weight: 600;
            border: none; border-radius: 8px;
            cursor: pointer; transition: background .2s;
            text-decoration: none; display: inline-block;
        }
        .btn-save:hover { background: #2563eb; }
        .btn-cancel {
            padding: 10px 28px;
            background: #6b7280; color: #fff;
            font-size: 0.875rem; font-weight: 600;
            border: none; border-radius: 8px;
            cursor: pointer; text-decoration: none;
            display: inline-block;
        }

        /* ── Logout ── */
        .logout-card {
            background: #e8e8e8;
            border-radius: 20px;
            padding: 48px 32px;
            max-width: 520px;
            text-align: center;
        }
        .logout-avatar-wrap { position: relative; width: 80px; height: 80px; margin: 0 auto 14px; }
        .logout-avatar-wrap img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
        .logout-avatar-wrap svg { width: 80px; height: 80px; color: #aaa; }
        .online-dot {
            position: absolute; bottom: 4px; right: 4px;
            width: 14px; height: 14px;
            background: #22c55e;
            border-radius: 50%; border: 2px solid #e8e8e8;
        }
        .logout-email { font-size: 0.85rem; color: #555; margin-bottom: 12px; }
        .logout-msg   { font-size: 0.95rem; color: #333; line-height: 1.6; margin-bottom: 24px; }

        .btn-logout {
            width: 100%; padding: 14px;
            background: #e53e3e; color: #fff;
            font-size: 0.95rem; font-weight: 600;
            border: none; border-radius: 50px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 10px;
            transition: background .2s;
        }
        .btn-logout:hover { background: #c53030; }
        .btn-logout svg { width: 18px; height: 18px; }

        .btn-batal {
            width: 100%; padding: 14px;
            background: #fff; color: #333;
            font-size: 0.95rem; font-weight: 600;
            border: 1.5px solid #ddd; border-radius: 50px;
            cursor: pointer; text-decoration: none; display: block;
            transition: background .2s;
        }
        .btn-batal:hover { background: #f5f5f5; }

        /* Alerts */
        .alert { padding: 10px 14px; border-radius: 8px; font-size: 0.82rem; margin-bottom: 16px; }
        .alert-success { background: #f0fff4; border: 1px solid #b7ebc8; color: #1e8c4a; }
        .alert-error   { background: #fff0f0; border: 1px solid #f5c6c6; color: #c0392b; }

        /* Loading spinner */
        .spin { display: inline-block; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 900px) {
            .sidebar   { display: none; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ═══════ SIDEBAR ═══════ -->
<aside class="sidebar">
    <div class="sidebar-user" id="sidebarUser">
        {{-- Skeleton saat load --}}
        <div class="skeleton sk-avatar" style="margin-bottom:12px"></div>
        <div class="skeleton sk-name"></div>
        <div class="skeleton sk-email" style="margin-top:6px"></div>
    </div>

    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <!-- Home (feed) -->
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            Home
        </a>

        <!-- Datasets -->
        <a href="{{ route('datasets.index') }}" class="nav-link {{ request()->routeIs('datasets*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
            Datasets
        </a>

        <!-- Profile (Public) – lihat profil diri sendiri seperti orang lain -->
        <a href="{{ route('profile.public', auth()->user()->username) }}" class="nav-link {{ request()->routeIs('profile.public') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            Profile
        </a>

        <!-- Settings (Edit profil) -->
        <a href="{{ route('profile.settings') }}" class="nav-link {{ request()->routeIs('profile.settings') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
                <path d="M17 4l2 2-4 4-2-2 4-4z"/>
                <path d="M7 12l-4 4 2 2 4-4-2-2z"/>
            </svg>
            Settings
        </a>

        <!-- Logout -->
        <a href="{{ route('logout.confirm') }}" class="nav-link {{ request()->routeIs('logout*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            LogOut
        </a>
    </nav>
</aside>

<!-- ═══════ MAIN ═══════ -->
<main class="main">
    @yield('content')
</main>

<script>
// ─── Ambil data user dari API lalu isi sidebar ───────────────────────────────
(async () => {
    try {
        const res  = await fetch('/api/dashboard', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Unauthorized');
        const data = await res.json();
        const u    = data.user;

        const avatarSrc = u.photo
            ? u.photo
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=444&color=fff&size=80`;

        document.getElementById('sidebarUser').innerHTML = `
            <div class="avatar-wrap">
                <img src="${avatarSrc}" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=444&color=fff&size=80'">
                <span class="notif-dot"></span>
            </div>
            <div class="sidebar-name">${u.username ?? u.name}</div>
            <div class="sidebar-email">${u.email}</div>
        `;

        // Simpan di sessionStorage buat halaman lain pakai
        sessionStorage.setItem('ss_user', JSON.stringify(u));
    } catch (e) {
        console.warn('Sidebar: gagal ambil user', e);
    }
})();
</script>

@stack('scripts')
</body>
</html>