@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<style>
    .profile-edit-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 40px;
    }
    .profile-edit-header {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .profile-edit-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #e5e7eb;
    }
    .profile-edit-title h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }
    .profile-edit-title p {
        color: #6b7280;
        margin: 4px 0 0;
        font-size: 14px;
    }
    .profile-edit-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px 40px;
    }
    .profile-edit-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .profile-edit-field.full-width {
        grid-column: 1 / -1;
    }
    .profile-edit-field label {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }
    .profile-edit-field input,
    .profile-edit-field select {
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        background: #f9fafb;
        transition: 0.2s;
    }
    .profile-edit-field input:focus,
    .profile-edit-field select:focus {
        border-color: #2563eb;
        outline: none;
        background: white;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .profile-edit-field input[readonly] {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }
    .profile-edit-field .hint {
        font-size: 12px;
        color: #9ca3af;
    }
    .profile-edit-photo-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .profile-edit-photo-wrapper img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }
    .profile-edit-photo-wrapper input[type="file"] {
        border: none;
        padding: 8px 0;
        background: transparent;
    }
    .profile-edit-actions {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    .btn-save {
        background: #2563eb;
        color: white;
        padding: 10px 32px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-save:hover { background: #1d4ed8; }
    .btn-save:disabled { opacity: 0.6; cursor: not-allowed; }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        padding: 10px 32px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: 0.2s;
    }
    .btn-cancel:hover { background: #e5e7eb; }
    .alert-edit {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .alert-edit.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-edit.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .alert-edit.hidden { display: none; }
    .loading-spinner {
        text-align: center;
        padding: 40px 0;
        color: #6b7280;
    }
    @media (max-width: 700px) {
        .profile-edit-grid { grid-template-columns: 1fr; }
        .profile-edit-header { flex-direction: column; text-align: center; }
        .profile-edit-actions { flex-direction: column; }
        .profile-edit-actions a, .profile-edit-actions button {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="profile-edit-container">
    <div id="edit-loading" class="loading-spinner">Memuat data profil...</div>

    <div id="edit-form-wrapper" style="display:none;">
        <div class="profile-edit-header">
            <img id="avatarPreview" src="" alt="Avatar" class="profile-edit-avatar">
            <div class="profile-edit-title">
                <h2>Edit Profil</h2>
                <p>Perbarui informasi akun Anda</p>
            </div>
        </div>

        <div id="editAlert" class="alert-edit hidden"></div>

        <form id="editForm" enctype="multipart/form-data">
            @csrf
            <div class="profile-edit-grid">
                <!-- Nama Lengkap -->
                <div class="profile-edit-field">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <!-- No Telp / Username (pakai username) -->
                <div class="profile-edit-field">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" required>
                    <span class="hint">Minimal 3 karakter, tanpa spasi</span>
                </div>

                <!-- Bahasa (pilihan statis) -->
                <div class="profile-edit-field">
                    <label for="language">Bahasa</label>
                    <select id="language" name="language">
                        <option value="id">Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>

                <!-- Nama panggilan (pakai bio sebagai pengganti) -->
                <div class="profile-edit-field">
                    <label for="bio">Nama panggilan / Bio</label>
                    <input type="text" id="bio" name="bio" placeholder="Tulis bio singkat...">
                </div>

                <!-- Negara -->
                <div class="profile-edit-field">
                    <label for="country">Negara</label>
                    <input type="text" id="country" name="country" placeholder="Indonesia" value="Indonesia">
                </div>

                <!-- Zona Waktu -->
                <div class="profile-edit-field">
                    <label for="timezone">Zona Waktu</label>
                    <select id="timezone" name="timezone">
                        <option value="WIB">(WIB) UTC+07:00</option>
                        <option value="WITA">(WITA) UTC+08:00</option>
                        <option value="WIT">(WIT) UTC+09:00</option>
                    </select>
                </div>

                <!-- Institusi (tambahan dari Figma sebenarnya tidak ada, tapi saya masukkan karena ada di database) -->
                <div class="profile-edit-field">
                    <label for="institution">Institusi</label>
                    <input type="text" id="institution" name="institution" placeholder="Nama institusi/universitas">
                </div>

                <!-- Lokasi (tambahan) -->
                <div class="profile-edit-field">
                    <label for="location">Lokasi</label>
                    <input type="text" id="location" name="location" placeholder="Kota, provinsi">
                </div>

                <!-- Foto Profil (full width) -->
                <div class="profile-edit-field full-width">
                    <label>Foto Profil</label>
                    <div class="profile-edit-photo-wrapper">
                        <img id="photoPreview" src="" alt="Preview">
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <span class="hint">Maksimal 2MB, format JPG/PNG</span>
                    </div>
                </div>
            </div>

            <div class="profile-edit-actions">
                <a href="{{ route('profile.public', auth()->user()->username) }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-save" id="btnSubmit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const fetchProfile = () => {
        fetch('{{ url("/me-data") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) throw new Error('Gagal mengambil profil');
            return res.json();
        })
        .then(data => {
            const user = data.user;
            document.getElementById('edit-loading').style.display = 'none';
            document.getElementById('edit-form-wrapper').style.display = 'block';

            document.getElementById('name').value = user.name || '';
            document.getElementById('username').value = user.username || '';
            document.getElementById('bio').value = user.bio || '';
            document.getElementById('institution').value = user.institution || '';
            document.getElementById('location').value = user.location || '';

            // Set foto preview
            const photoUrl = user.photo ? '/storage/' + user.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)+'&background=random';
            document.getElementById('avatarPreview').src = photoUrl;
            document.getElementById('photoPreview').src = photoUrl;

            // Set bahasa & timezone (default)
            document.getElementById('language').value = 'id';
            document.getElementById('timezone').value = 'WIB';
        })
        .catch(err => {
            document.getElementById('edit-loading').innerHTML = '<span style="color:#dc2626;">Gagal memuat profil: ' + err.message + '</span>';
        });
    };

    // Preview foto saat dipilih
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('photoPreview').src = ev.target.result;
                document.getElementById('avatarPreview').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Submit form
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('btnSubmit');
        const alertDiv = document.getElementById('editAlert');

        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
        alertDiv.classList.add('hidden');

        fetch('{{ url("/me-update") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            credentials: 'same-origin',
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            if (status !== 200) throw new Error(data.message || 'Update gagal');
            alertDiv.className = 'alert-edit success';
            alertDiv.textContent = '✅ Profil berhasil diperbarui!';
            alertDiv.classList.remove('hidden');
            // refresh foto
            const user = data.user;
            const photoUrl = user.photo ? '/storage/' + user.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)+'&background=random';
            document.getElementById('avatarPreview').src = photoUrl;
            document.getElementById('photoPreview').src = photoUrl;
            setTimeout(() => alertDiv.classList.add('hidden'), 4000);
        })
        .catch(err => {
            alertDiv.className = 'alert-edit error';
            alertDiv.textContent = '⚠️ ' + err.message;
            alertDiv.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Simpan Perubahan';
        });
    });

    fetchProfile();
</script>
@endsection