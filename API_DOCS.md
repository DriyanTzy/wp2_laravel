# SurveySwap API Documentation

Base URL: `http://localhost:8000/api`

Untuk endpoint yang butuh login, tambahkan header:
```
Authorization: Bearer {token}
Content-Type: application/json
```

---

## AUTH

### Register
**POST** `/api/register`
```json
// Request
{ "name": "Driyan", "username": "driyan90", "email": "driyan@email.com", "password": "pass1234", "password_confirmation": "pass1234" }

// Response 201
{ "message": "Registrasi berhasil.", "token": "xxx", "user": { "id": 1, "name": "Driyan", "username": "driyan90", "points": 0 } }
```

### Login
**POST** `/api/login`
```json
// Request
{ "username": "driyan90", "password": "pass1234" }

// Response 200
{ "message": "Login berhasil.", "token": "xxx", "user": { ... } }

// Response 401 (salah password)
{ "message": "Username atau password salah." }
```

### Logout 🔒
**POST** `/api/logout`
```json
// Response 200
{ "message": "Logout berhasil." }
```

### Cek user sendiri 🔒
**GET** `/api/me`
```json
// Response 200
{ "user": { "id": 1, "name": "Driyan", "username": "driyan90", "points": 5, ... } }
```

---

## DASHBOARD 🔒

### Statistik
**GET** `/api/dashboard`
```json
// Response 200
{
  "stats": {
    "total_surveys": 3,
    "total_responses": 42,
    "total_reach": 38,
    "total_datasets": 2,
    "points": 10
  },
  "active_surveys": [ { "id": 1, "title": "Survey Saya", "responses_count": 15 } ],
  "user": { "name": "Driyan", "username": "driyan90", "photo": null, "email": "..." }
}
```

---

## PROFILE

### Lihat profil user (publik)
**GET** `/api/profile/{username}`
```json
// Response 200
{
  "user": { "id": 1, "name": "Driyan", "username": "driyan90", "bio": "...", "photo": "url" },
  "posts": [ { "id": 1, "title": "Post saya", "survey_link": "https://..." } ]
}
```

### Lihat profil sendiri 🔒
**GET** `/api/profile`

### Edit profil 🔒
**PUT** `/api/profile` (gunakan `multipart/form-data` kalau upload foto)
```
name, username, bio, photo (file gambar, opsional)
```

### Ganti password 🔒
**PUT** `/api/profile/password`
```json
{ "current_password": "lama1234", "password": "baru1234", "password_confirmation": "baru1234" }
```

---

## DATASETS

### List semua dataset (publik)
**GET** `/api/datasets`
```json
{ "datasets": [ { "id": 1, "title": "Contoh 1", "class": "X PPLG 1", "present_count": 10, "points_required": 5 } ] }
```

### Detail dataset (publik)
**GET** `/api/datasets/{id}`

### Upload dataset baru 🔒
**POST** `/api/datasets` (gunakan `multipart/form-data`)
```
title, class, description, file (wajib), thumbnail (opsional), points_required
```

### Ambil dataset pakai poin 🔒
**POST** `/api/datasets/{id}/access`
```json
// Response 200 (berhasil)
{ "message": "Dataset berhasil diakses.", "file_url": "http://...", "points_remaining": 5 }

// Response 403 (poin kurang)
{ "message": "Poin tidak cukup. Kamu butuh 5 poin, kamu punya 2 poin." }
```

### Hapus dataset 🔒
**DELETE** `/api/datasets/{id}`

---

## SURVEYS

### List semua survey aktif (publik)
**GET** `/api/surveys`

### Buat survey 🔒
**POST** `/api/surveys`
```json
{ "title": "Survey Saya", "description": "...", "link": "https://forms.google.com/...", "target_responses": 50 }
```

### Edit survey 🔒
**PUT** `/api/surveys/{id}`
```json
{ "title": "...", "is_active": false }
```

### Hapus survey 🔒
**DELETE** `/api/surveys/{id}`

### Catat isi survey + dapat poin 🔒
**POST** `/api/surveys/{id}/respond`
```json
// Response 200
{ "message": "Respons berhasil dicatat. Kamu mendapat 1 poin!", "points": 6, "survey_link": "https://..." }

// Response 409 (sudah pernah isi)
{ "message": "Kamu sudah pernah mengisi survey ini." }
```

---

## POSTS (di halaman profil)

### Buat post 🔒
**POST** `/api/posts`
```json
{ "title": "Survey terbaru gw", "content": "Tolong diisi ya!", "survey_link": "https://..." }
```

### Edit post 🔒
**PUT** `/api/posts/{id}`

### Hapus post 🔒
**DELETE** `/api/posts/{id}`

---

## Cara pakai di HTML + JS

```javascript
// Simpan token setelah login
const token = localStorage.getItem('token');

// Contoh fetch dengan token
const res = await fetch('http://localhost:8000/api/dashboard', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});
const data = await res.json();
```

🔒 = butuh login (kirim token di header Authorization)
