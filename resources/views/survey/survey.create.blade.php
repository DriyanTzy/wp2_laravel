<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Survey</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { display: flex; min-height: 100vh; font-family: 'Segoe UI', sans-serif; background: #f5f5f5; }

        .sidebar { width: 260px; background: #111; color: #fff; padding: 32px 24px; display: flex; flex-direction: column; gap: 8px; position: fixed; height: 100vh; }
        .sidebar .profile { margin-bottom: 32px; }
        .sidebar .profile img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .sidebar .profile h3 { margin-top: 12px; font-size: 16px; }
        .sidebar .profile p { font-size: 12px; color: #888; }
        .sidebar a { display: flex; align-items: center; gap: 10px; color: #ccc; text-decoration: none; padding: 10px 12px; border-radius: 8px; font-size: 14px; }
        .sidebar a:hover { background: #222; color: #fff; }

        .main { margin-left: 260px; padding: 40px; flex: 1; }
        .main h1 { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .main h1 span { color: #f5a623; }
        .main .date { color: #999; font-size: 13px; margin-bottom: 32px; }

        .form-card { background: #fff; border-radius: 16px; padding: 32px; max-width: 700px; border: 1px solid #eee; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
        input[type="text"], input[type="url"], textarea { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: border .2s; }
        input:focus, textarea:focus { border-color: #7c3aed; }
        textarea { resize: vertical; min-height: 120px; }
        .upload-area { border: 2px dashed #ddd; border-radius: 10px; padding: 32px; text-align: center; cursor: pointer; transition: border .2s; }
        .upload-area:hover { border-color: #7c3aed; }
        .upload-area input { display: none; }
        .upload-area p { color: #999; font-size: 13px; margin-top: 8px; }
        #preview { max-width: 100%; border-radius: 8px; margin-top: 12px; display: none; }
        .btn-submit { background: #111; color: #fff; padding: 12px 28px; border: none; border-radius: 10px; font-size: 14px; cursor: pointer; }
        .btn-submit:hover { background: #333; }
        .error { color: #dc2626; font-size: 12px; margin-top: 4px; }
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
    <a href="{{ route('surveys.index') }}">≡ Datasets</a>
    <a href="#">👤 Profile</a>
    <a href="#">↪ LogOut</a>
</aside>

<main class="main">
    <h1>Survey <span>Swap</span></h1>
    <p class="date">{{ now()->format('d M, Y') }}</p>

    <a href="{{ route('surveys.index') }}" class="back">← Kembali</a>

    <div class="form-card">
        <form action="{{ route('surveys.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Judul Survey</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul survey...">
                @error('title') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Gambar Banner</label>
                <div class="upload-area" onclick="document.getElementById('imageInput').click()">
                    <input type="file" id="imageInput" name="image" accept="image/*" onchange="previewImage(event)">
                    <div id="uploadText">
                        🖼️
                        <p>Klik untuk upload gambar (JPG, PNG, WEBP — maks 5MB)</p>
                    </div>
                    <img id="preview" src="" alt="Preview">
                </div>
                @error('image') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" placeholder="Tulis deskripsi survey...">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Link Survey (Google Form, dll)</label>
                <input type="url" name="link" value="{{ old('link') }}" placeholder="https://docs.google.com/forms/...">
                @error('link') <p class="error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-submit">Simpan Survey</button>
        </form>
    </div>
</main>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const preview = document.getElementById('preview');
        const uploadText = document.getElementById('uploadText');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        uploadText.style.display = 'none';
    }
</script>

</body>
</html>