<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CetakController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return redirect()->route(match(auth()->user()->role) {
        'admin'   => 'admin.laporan',
        'petugas' => 'petugas.daftar-pengajuan',
        default   => 'siswa.katalog',
    });
});

//Sementara
Route::get('/glitchtiptest', function () {
    throw new Exception('Glitchtip Test Error');
});

//Sementara
Route::middleware('auth')->get('/test-error', function () {
    throw new \Exception('Mockup error untuk verifikasi GlitchTip — dipicu pada ' . now());
})->name('test-error');

require __DIR__.'/auth.php';

// ── Admin ────────────────────────────────────────────────
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/laporan', [LaporanController::class,'index'])->name('laporan');

    Route::get('/alat/import',   [AlatController::class,'importForm'])->name('alat.import.form');
    Route::post('/alat/import',  [AlatController::class,'import'])->name('alat.import');
    Route::get('/alat/template', [AlatController::class,'template'])->name('alat.template');
    Route::resource('alat', AlatController::class);

    Route::get('/siswa/import',   [AdminSiswaController::class,'importForm'])->name('siswa.import.form');
    Route::post('/siswa/import',  [AdminSiswaController::class,'import'])->name('siswa.import');
    Route::get('/siswa/template', [AdminSiswaController::class,'template'])->name('siswa.template');
    Route::resource('siswa', AdminSiswaController::class);

    Route::resource('users', UserController::class);
});

// ── Petugas ──────────────────────────────────────────────
Route::middleware(['auth','role:admin,petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/pengajuan',                        [PetugasController::class,    'daftarPengajuan'])->name('daftar-pengajuan');
    Route::patch('/pengajuan/{peminjaman}/acc',     [PetugasController::class,    'acc'])->name('acc');
    Route::patch('/pengajuan/{peminjaman}/tolak',   [PetugasController::class,    'tolak'])->name('tolak');
    Route::get('/pengembalian/{peminjaman}/create', [PengembalianController::class,'create'])->name('pengembalian.create');
    Route::post('/pengembalian/{peminjaman}',       [PengembalianController::class,'store'])->name('pengembalian.store');
});

// ── Cetak ────────────────────────────────────────────────
Route::middleware(['auth','role:admin,petugas'])->prefix('cetak')->name('cetak.')->group(function () {
    Route::get('/',                   [CetakController::class,'index'])->name('index');
    Route::get('/history-peminjaman', [CetakController::class,'historyPeminjaman'])->name('history');
    Route::get('/belum-kembali',      [CetakController::class,'belumKembali'])->name('belum-kembali');
    Route::get('/terlambat',          [CetakController::class,'terlambat'])->name('terlambat');
    Route::get('/inventaris-alat',    [CetakController::class,'inventarisAlat'])->name('inventaris');
    Route::get('/alat-rusak',         [CetakController::class,'alatRusak'])->name('alat-rusak');
});

// ── Siswa ────────────────────────────────────────────────
Route::middleware(['auth','role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/katalog', [SiswaController::class,'katalog'])->name('katalog');
    Route::get('/pinjam',  [SiswaController::class,'createPeminjaman'])->name('peminjaman.create');
    Route::post('/pinjam', [SiswaController::class,'storePeminjaman'])->name('peminjaman.store');
    Route::get('/riwayat', [SiswaController::class,'riwayat'])->name('riwayat');
});
