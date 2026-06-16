<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $survey->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; min-height: 100vh; font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }

        .sidebar { width: 260px; background: #111; color: #fff; padding: 32px 24px; display: flex; flex-direction: column; gap: 8px; position: fixed; height: 100vh; }
        .sidebar .profile { margin-bottom: 32px; }
        .sidebar .profile img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .sidebar .profile h3 { margin-top: 12px; font-size: 16px; }
        .sidebar .profile p { font-size: 12px; color: #888; }
        .sidebar a { display: flex; align-items: center; gap: 10px; color: #ccc; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; }
        .sidebar a:hover, .sidebar a.active { background: #222; color: #fff; }

        .main { margin-left: 260px; padding: 40px; flex: 1; }
        .main h1 { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .main h1 span { color: #f5a623; }
        .main .date { color: #999; font-size: 13px; margin-bottom: 32px; }

        .card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #eee; max-width: 860px; }
        .card img { width: 100%; max-height: 340px; object-fit: cover; }
        .card-body { padding: 28px 32px; }
        .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .card-top h2 { font-size: 18px; font-weight: 700; }
        .badge { background: #ede9fe; color: #7c3aed; font-size: 12px; padding: 4px 12px; border-radius: 20px; }
        .description { font-size: 15px; color: #333; line-height: 1.7; margin-bottom: 20px; }
        .survey-link { color: #0ea5e9; font-size: 14px; text-decoration: none; word-break: break-all; }
        .survey-link:hover { text-decoration: underline; }
        .back { display: inline-block; margin-bottom: 24px; color: #666; text-decoration: none; font-size: 14px; }
        .back:hover { color: #111; }
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
    <h1>Survey <span>Swap</span></h1>
    <p class="date">{{ $survey->created_at->format('d M, Y') }}</p>

    <a href="{{ route('surveys.index') }}" class="back">← Kembali</a>

    <div class="card">
        @if($survey->image_path)
            <img src="{{ asset('storage/' . $survey->image_path) }}" alt="{{ $survey->title }}">
        @endif

        <div class="card-body">
            <div class="card-top">
                <h2>Deskripsi</h2>
                <span class="badge">📋 Survei</span>
            </div>
            <p class="description">{{ $survey->description }}</p>

            @if($survey->link)
                <a href="{{ $survey->link }}" class="survey-link" target="_blank" rel="noopener">
                    {{ $survey->link }}
                </a>
            @endif
        </div>
    </div>
</main>

</body>
</html>