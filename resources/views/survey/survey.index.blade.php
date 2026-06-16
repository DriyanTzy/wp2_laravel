<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Survey Swap</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; min-height: 100vh; font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }

        /* Sidebar */
        .sidebar { width: 260px; background: #111; color: #fff; padding: 32px 24px; display: flex; flex-direction: column; gap: 8px; position: fixed; height: 100vh; }
        .sidebar .profile { margin-bottom: 32px; }
        .sidebar .profile img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .sidebar .profile h3 { margin-top: 12px; font-size: 16px; }
        .sidebar .profile p { font-size: 12px; color: #888; }
        .sidebar a { display: flex; align-items: center; gap: 10px; color: #ccc; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #222; color: #fff; }

        /* Main */
        .main { margin-left: 260px; padding: 40px; flex: 1; }
        .main h1 { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .main h1 span { color: #f5a623; }
        .main .date { color: #999; font-size: 13px; margin-bottom: 32px; }

        /* Grid */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #eee; transition: box-shadow .2s; }
        .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .card img { width: 100%; height: 180px; object-fit: cover; }
        .card .no-image { width: 100%; height: 180px; background: #1a1a2e; display: flex; align-items: center; justify-content: center; color: #555; font-size: 13px; }
        .card-body { padding: 20px; }
        .card-body .badge { display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 11px; padding: 3px 10px; border-radius: 20px; margin-bottom: 10px; }
        .card-body h2 { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
        .card-body p { font-size: 13px; color: #666; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .card-body a.btn { display: inline-block; margin-top: 14px; background: #111; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; text-decoration: none; }
        .card-body a.btn:hover { background: #333; }

        /* Tombol tambah */
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn-add { background: #111; color: #fff; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 14px; }
        .btn-add:hover { background: #333; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="profile">
        <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" alt="foto">
        <h3>{{ auth()->user()->name }}</h3>
        <p>{{ auth()->user()->email }}</p>
    </div>
    <a href="#">⊞ DashBoard</a>
    <a href="#">⌂ Home</a>
    <a href="{{ route('surveys.index') }}" class="active">≡ Datasets</a>
    <a href="#">👤 Profile</a>
    <a href="#">↪ LogOut</a>
</aside>

<main class="main">
    <div class="top-bar">
        <div>
            <h1>Survey <span>Swap</span></h1>
            <p class="date">{{ now()->format('d M, Y') }}</p>
        </div>
        <a href="{{ route('surveys.create') }}" class="btn-add">+ Buat Survey</a>
    </div>

    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid">
        @forelse($surveys as $survey)
        <div class="card">
            @if($survey->image_path)
                <img src="{{ asset('storage/' . $survey->image_path) }}" alt="{{ $survey->title }}">
            @else
                <div class="no-image">Tidak ada gambar</div>
            @endif
            <div class="card-body">
                <span class="badge">📋 Survei</span>
                <h2>{{ $survey->title }}</h2>
                <p>{{ $survey->description }}</p>
                <a href="{{ route('surveys.show', $survey) }}" class="btn">Lihat Detail →</a>
            </div>
        </div>
        @empty
            <p style="color:#999;">Belum ada survey.</p>
        @endforelse
    </div>
</main>

</body>
</html>