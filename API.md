# LabTrack REST API

Dokumentasi ini menjelaskan seluruh endpoint API yang ditambahkan di atas
aplikasi web LabTrack (Laravel 11) tanpa mengubah fitur web/Blade yang
sudah ada. API menggunakan **Laravel Sanctum** (Bearer Token) — cocok
untuk diuji lewat Bruno, dikonsumsi aplikasi mobile, atau frontend SPA
terpisah di masa depan.

- Base URL (lokal): `http://localhost:8000/api`
- Auth: `Authorization: Bearer {token}`
- Format response: JSON, selalu mengandung field `success` (boolean)
- Semua request/response body: `Content-Type: application/json` (kecuali endpoint upload foto yang memakai `multipart/form-data`)

---

## 1. Instalasi & Setup

```bash
# 1. Clone repo
git clone https://github.com/Ditiannd/labtrack.git
cd labtrack

# 2. Install dependency PHP (termasuk Laravel Sanctum yang baru ditambahkan)
composer install

# 3. Install dependency JS & build asset (untuk sisi web/Blade)
npm install && npm run build

# 4. Environment
cp .env.example .env      # jika belum ada .env
php artisan key:generate

# 5. Sesuaikan koneksi database di .env, lalu migrate + seed
php artisan migrate:fresh --seed

# 6. Storage link (foto alat)
php artisan storage:link

# 7. Jalankan server
php artisan serve
```

> Perintah `composer install` di atas otomatis menginstal **laravel/sanctum**
> (sudah ditambahkan ke `composer.json`) dan mem-publish migration
> `personal_access_tokens` (sudah disertakan manual di folder migrations
> agar tidak perlu `artisan vendor:publish` tambahan).

### Akun default (hasil seeder)

| Role    | Email             | Password |
|---------|-------------------|----------|
| Admin   | admin@lab.com     | password |
| Petugas | petugas@lab.com   | password |
| Siswa   | siswa@lab.com     | password |
| Siswa 2 | ani@lab.com       | password |

---

## 2. Integrasi dengan Bruno

1. Buka aplikasi **Bruno**.
2. `Open Collection` → pilih folder `bruno/LabTrack-API` di dalam repo ini.
3. Pilih environment **Local** (pojok kanan atas), pastikan `baseUrl`
   sesuai server kamu (default `http://localhost:8000`).
4. Jalankan request **Auth → Login (Admin)** (atau Petugas/Siswa).
   Token hasil login otomatis tersimpan ke variabel collection `token`
   lewat script *post-response*, jadi seluruh request lain (yang sudah
   diset auth Bearer `{{token}}` di level collection) langsung bisa
   dipakai tanpa copy-paste manual.
5. Jalankan request lain sesuai folder: Alat, Siswa, Users, Peminjaman,
   Pengembalian, Kerusakan, Dashboard.

**Skenario alur peminjaman end-to-end** (urutan yang disarankan untuk testing):

1. `Auth → Login (Siswa)`
2. `Peminjaman → Ajukan Peminjaman (siswa)` → status `pending`
3. `Auth → Login (Petugas)`
4. `Peminjaman → List Peminjaman` (lihat id pengajuan yang baru)
5. `Peminjaman → ACC Peminjaman` → status `acc`, stok alat berkurang
6. `Pengembalian → Catat Pengembalian - Kondisi Baik/Rusak` → status `selesai`, stok alat bertambah kembali (dan tercatat di `Kerusakan` jika rusak)

---

## 3. Autentikasi

### `POST /api/login` — Publik

Login dan mendapatkan Bearer Token.

**Request body**
```json
{
  "email": "admin@lab.com",
  "password": "password",
  "device_name": "bruno"
}
```

**Response 200**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": { "id": 1, "name": "Admin", "email": "admin@lab.com", "role": "admin" },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

**Response 401** — email/password salah.

### `GET /api/me` — Auth wajib

Data user yang sedang login.

### `POST /api/logout` — Auth wajib

Mencabut token yang sedang dipakai (device/sesi saat ini saja).

### `POST /api/logout-all` — Auth wajib

Mencabut **semua** token milik user (logout dari semua device).

---

## 4. Matriks Otorisasi Role

| Endpoint                                   | Admin | Petugas | Siswa |
|---------------------------------------------|:---:|:---:|:---:|
| `GET /alat`, `GET /alat/{id}`                | ✅ | ✅ | ✅ |
| `POST/PUT/DELETE /alat`                      | ✅ | ❌ | ❌ |
| `GET/POST/PUT/DELETE /siswa`                 | ✅ | ❌ | ❌ |
| `GET/POST/PUT/DELETE /users`                 | ✅ | ❌ | ❌ |
| `GET /dashboard`                             | ✅ | ❌ | ❌ |
| `GET /peminjaman` (semua data)                | ✅ | ✅ | 🔸 hanya miliknya |
| `POST /peminjaman` (ajukan)                   | ❌ | ❌ | ✅ |
| `PATCH /peminjaman/{id}/acc`                  | ✅ | ✅ | ❌ |
| `PATCH /peminjaman/{id}/tolak`                | ✅ | ✅ | ❌ |
| `POST /peminjaman/{id}/pengembalian`          | ✅ | ✅ | ❌ |
| `GET /kerusakan`                              | ✅ | ✅ | ❌ |

Role dicek lewat middleware `api.role:...` (lihat `app/Http/Middleware/EnsureApiRole.php`).
Request tanpa token → **401**. Token valid tapi role tidak sesuai → **403**.

---

## 5. Endpoint — Alat (Inventaris)

### `GET /api/alat`
Query params opsional: `q`, `kategori`, `kondisi` (`baik`|`rusak`), `tersedia` (`true` → stok>0 & kondisi baik), `per_page`.

### `GET /api/alat/{id}`
Detail 1 alat + riwayat kerusakan.

### `POST /api/alat` — admin
```json
{
  "nama_alat": "Mikroskop Binokuler",
  "kategori": "Biologi",
  "stok": 10,
  "kondisi": "baik",
  "lokasi": "Lab Biologi - Rak A",
  "deskripsi": "..."
}
```
Untuk sertakan foto, kirim sebagai `multipart/form-data` dengan field `foto` (image, max 2MB).

### `PUT /api/alat/{id}` — admin
Partial update (field yang tidak dikirim tidak berubah).

> ⚠️ **Update + foto sekaligus**: PHP tidak mem-parsing body `multipart/form-data`
> pada method `PUT`/`PATCH` native. Gunakan `POST /api/alat/{id}` dengan field
> tambahan `_method=PUT` (method spoofing) — sudah disiapkan sebagai request
> terpisah di Bruno ("Update Alat dengan Foto").

### `DELETE /api/alat/{id}` — admin

---

## 6. Endpoint — Siswa (Admin mengelola akun siswa)

### `GET /api/siswa` — admin
Filter: `q`, `jurusan`, `kelas`, `per_page`.

### `POST /api/siswa` — admin
Membuat akun login (role `siswa`) + profil siswa sekaligus dalam satu transaction.
```json
{
  "nama": "Budi Santoso",
  "nis": "23001",
  "kelas": "XI RPL 1",
  "jurusan": "Rekayasa Perangkat Lunak",
  "angkatan": "2023",
  "email": "budi@lab.com",
  "password": "password"
}
```

### `GET /api/siswa/{id}`, `PUT /api/siswa/{id}`, `DELETE /api/siswa/{id}` — admin
Hapus siswa otomatis menghapus akun user login-nya juga.

---

## 7. Endpoint — Users (Admin mengelola akun admin/petugas)

### `GET /api/users` — admin
Hanya menampilkan role `admin` & `petugas`.

### `POST /api/users` — admin
```json
{ "name": "Rina Petugas", "email": "rina@lab.com", "password": "password", "role": "petugas" }
```

### `GET/PUT/DELETE /api/users/{id}` — admin
Admin tidak bisa menghapus akunnya sendiri (validasi server-side).

---

## 8. Endpoint — Peminjaman (Alur Inti)

### `GET /api/peminjaman`
- Admin/petugas: semua data. Siswa: otomatis hanya miliknya.
- Filter: `status` (`pending`|`acc`|`ditolak`|`selesai`), `per_page`.

### `GET /api/peminjaman/{id}`
Siswa hanya boleh melihat miliknya sendiri (403 jika bukan).

### `POST /api/peminjaman` — siswa
```json
{
  "id_alat": 1,
  "tanggal_pinjam": "2026-07-20",
  "tanggal_kembali": "2026-07-27",
  "jumlah": 1,
  "catatan_siswa": "Untuk praktikum"
}
```
Validasi otomatis: stok cukup, alat tidak rusak, dan siswa belum punya
peminjaman aktif (`pending`/`acc`) untuk alat yang sama. Status awal selalu `pending`.

### `PATCH /api/peminjaman/{id}/acc` — admin, petugas
Menyetujui pengajuan. Stok alat dikurangi (DB transaction + row lock,
aman dari race condition approval bersamaan).

### `PATCH /api/peminjaman/{id}/tolak` — admin, petugas
```json
{ "catatan_petugas": "Stok sedang dipakai kelas lain." }
```

---

## 9. Endpoint — Pengembalian

### `POST /api/peminjaman/{id}/pengembalian` — admin, petugas
Peminjaman harus berstatus `acc`.
```json
{
  "tanggal_kembali_aktual": "2026-07-27",
  "kondisi": "baik",
  "catatan": "Alat lengkap"
}
```
Jika `kondisi = "rusak"`, field `deskripsi_kerusakan` **wajib**:
```json
{
  "tanggal_kembali_aktual": "2026-07-27",
  "kondisi": "rusak",
  "deskripsi_kerusakan": "Lensa retak",
  "catatan": "Perlu penggantian"
}
```
Efek otomatis: status peminjaman → `selesai`, stok alat bertambah kembali,
dan jika rusak → kondisi alat diubah `rusak` + tercatat di riwayat kerusakan.

---

## 10. Endpoint — Kerusakan

### `GET /api/kerusakan` — admin, petugas
Riwayat kerusakan alat (terbaru dulu). Filter: `id_alat`, `per_page`.

---

## 11. Endpoint — Dashboard

### `GET /api/dashboard` — admin
Statistik ringkas: total peminjaman per status, total alat & stok, alat
rusak, peminjaman terbaru, alat paling sering dipinjam, peminjaman
terlambat, trend 7 hari terakhir, distribusi per jurusan, dan stok per
kategori. Struktur data sama persis dengan yang dipakai halaman web
`admin.laporan`, hanya dalam bentuk JSON.

---

## 12. Format Response & Error

**Sukses (single resource)**
```json
{ "success": true, "message": "...", "data": { ... } }
```

**Sukses (list/pagination)** — memakai Laravel API Resource pagination bawaan:
```json
{
  "success": true,
  "data": [ ... ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 }
}
```

**Error validasi (422)**
```json
{ "success": false, "message": "Validasi gagal.", "errors": { "email": ["The email field is required."] } }
```

**Unauthenticated (401)**
```json
{ "success": false, "message": "Unauthenticated. Token tidak valid atau belum login." }
```

**Forbidden / role tidak sesuai (403)**
```json
{ "success": false, "message": "Forbidden. Role Anda (siswa) tidak memiliki akses ke resource ini.", "required_role": ["admin","petugas"] }
```

---

## 13. Struktur Kode yang Ditambahkan

```
app/Http/Controllers/Api/     → AuthController, AlatController, SiswaController,
                                  UserController, PeminjamanController,
                                  PengembalianController, KerusakanController,
                                  DashboardController
app/Http/Requests/Api/        → FormRequest untuk validasi tiap endpoint tulis
app/Http/Resources/           → JsonResource untuk bentuk output konsisten
app/Http/Middleware/
  EnsureApiRole.php           → middleware role khusus API (selalu balas JSON)
routes/api.php                → seluruh route /api/*
config/sanctum.php            → konfigurasi Laravel Sanctum
database/migrations/
  ..._create_personal_access_tokens_table.php
bruno/LabTrack-API/           → koleksi Bruno siap pakai
```

Kontroler & route web (Blade) yang lama **tidak diubah sama sekali** —
API ini murni layer tambahan di atas model & business logic yang sudah ada.
