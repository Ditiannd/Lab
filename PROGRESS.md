# LabTrack — Status Progress & Panduan Lanjut

## ✅ SUDAH SELESAI

### REST API (Sanctum) — BARU
- `routes/api.php` — seluruh endpoint `/api/*` (auth, alat, siswa, users, peminjaman, pengembalian, kerusakan, dashboard)
- `app/Http/Controllers/Api/*` — controller API terpisah dari controller web
- `app/Http/Requests/Api/*`, `app/Http/Resources/*` — validasi & format JSON konsisten
- `app/Http/Middleware/EnsureApiRole.php` — role guard khusus API (selalu balas JSON)
- `bruno/LabTrack-API/` — koleksi Bruno siap import
- Dokumentasi lengkap: lihat `API.md`

### Views
- `resources/views/layouts/app.blade.php` — Layout utama iOS 18 style
- `resources/views/partials/sidebar.blade.php` — Sidebar per role
- `resources/views/auth/login.blade.php` — Halaman login
- `resources/views/admin/laporan.blade.php` — Dashboard admin
- `resources/views/admin/alat/index.blade.php` — List alat
- `resources/views/admin/alat/create.blade.php` — Form tambah alat
- `resources/views/admin/alat/edit.blade.php` — Form edit alat
- `resources/views/admin/siswa/index.blade.php` — List siswa
- `resources/views/admin/siswa/create.blade.php` — Form tambah siswa
- `resources/views/admin/siswa/edit.blade.php` — Form edit siswa
- `resources/views/admin/users/index.blade.php` — List user
- `resources/views/admin/users/create.blade.php` — Form tambah user
- `resources/views/admin/users/edit.blade.php` — Form edit user
- `resources/views/petugas/daftar-pengajuan.blade.php` — Validasi ACC/Tolak + modal detail
- `resources/views/petugas/pengembalian/create.blade.php` — Input pengembalian
- `resources/views/siswa/katalog.blade.php` — Katalog alat (grid cards)
- `resources/views/siswa/peminjaman/create.blade.php` — Form ajukan peminjaman
- `resources/views/siswa/riwayat.blade.php` — Riwayat peminjaman siswa

### Controllers
- `app/Http/Controllers/Admin/LaporanController.php`
- `app/Http/Controllers/Admin/AlatController.php`
- `app/Http/Controllers/Admin/SiswaController.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Controllers/PetugasController.php` — acc, tolak
- `app/Http/Controllers/PengembalianController.php` — input pengembalian + catat kerusakan
- `app/Http/Controllers/SiswaController.php` — katalog, create, store, riwayat
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` — redirect per role

### Models
- `User`, `Siswa`, `Alat`, `Peminjaman`, `Pengembalian`, `Kerusakan`

### Routes
- `routes/web.php` — Semua route dengan role middleware

### Migrations
- `2026_05_18_013745_create_pengembalian_table.php` — Diperbaiki
- `2026_05_18_013800_create_kerusakan_table.php` — Baru dibuat

### Seeder
- `database/seeders/DatabaseSeeder.php` — Admin, petugas, 2 siswa, 10 alat

---

## 🚀 CARA INSTALL & JALANKAN

```bash
# 1. Copy project ke server/hosting
# 2. Install dependencies
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env — isi DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Migrate & seed
php artisan migrate:fresh --seed

# 6. Storage link (untuk foto alat)
php artisan storage:link

# 7. Jalankan
php artisan serve
```

## 🔑 Akun Default Setelah Seed

| Role    | Email             | Password  |
|---------|-------------------|-----------|
| Admin   | admin@lab.com     | password  |
| Petugas | petugas@lab.com   | password  |
| Siswa   | siswa@lab.com     | password  |
| Siswa 2 | ani@lab.com       | password  |

---

## ⚠️ JIKA INGIN MELANJUTKAN (Panduan Sinkron AI)

Berikan prompt ini ke AI berikutnya:

> "Lanjutkan proyek Laravel LabTrack sistem peminjaman alat praktikum.
> Lihat file PROGRESS.md untuk daftar yang sudah selesai.
> Yang perlu dilanjutkan:
> 1. Tambahkan fitur export laporan PDF/Excel di admin
> 2. Tambahkan halaman kerusakan alat (list & detail)
> 3. Notifikasi badge pending di sidebar
> 4. Form reset password
> Struktur project: ada di folder ini. Gunakan style iOS 18 yang sama di layouts/app.blade.php"

