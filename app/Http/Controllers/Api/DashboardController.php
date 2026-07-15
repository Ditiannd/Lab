<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Siswa;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard  (admin)
     * Ringkasan statistik untuk kartu dashboard admin.
     */
    public function index()
    {
        $stats = [
            'total_peminjaman' => Peminjaman::count(),
            'pending' => Peminjaman::where('status', 'pending')->count(),
            'acc' => Peminjaman::where('status', 'acc')->count(),
            'selesai' => Peminjaman::where('status', 'selesai')->count(),
            'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
            'total_alat' => Alat::count(),
            'total_stok' => (int) Alat::sum('stok'),
            'alat_rusak' => Alat::where('kondisi', 'rusak')->count(),
            'total_siswa' => Siswa::count(),
            'total_petugas' => User::where('role', 'petugas')->count(),
        ];

        $peminjamanTerbaru = Peminjaman::with(['siswa', 'alat'])->latest()->take(8)->get();

        $alatPopuler = Peminjaman::selectRaw('id_alat, COUNT(*) as total')
            ->with('alat')
            ->groupBy('id_alat')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($row) => [
                'alat' => $row->alat?->nama_alat,
                'id_alat' => $row->id_alat,
                'total_dipinjam' => $row->total,
            ]);

        $terlambat = Peminjaman::with(['siswa', 'alat'])
            ->where('status', 'acc')
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->get();

        $trend = Peminjaman::selectRaw('DATE(created_at) as tgl, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get()
            ->keyBy('tgl');

        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $trendData[] = [
                'tanggal' => $d,
                'label' => now()->subDays($i)->format('d M'),
                'total' => $trend->get($d)?->total ?? 0,
            ];
        }

        $perJurusan = Siswa::selectRaw('jurusan, COUNT(*) as total')
            ->whereNotNull('jurusan')
            ->groupBy('jurusan')
            ->orderByDesc('total')
            ->get();

        $perKategori = Alat::selectRaw('kategori, COUNT(*) as jenis, SUM(stok) as total_stok')
            ->whereNotNull('kategori')->where('kategori', '!=', '')
            ->groupBy('kategori')
            ->orderByDesc('total_stok')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'peminjaman_terbaru' => \App\Http\Resources\PeminjamanResource::collection($peminjamanTerbaru),
                'alat_populer' => $alatPopuler,
                'terlambat' => \App\Http\Resources\PeminjamanResource::collection($terlambat),
                'trend_7_hari' => $trendData,
                'per_jurusan' => $perJurusan,
                'per_kategori' => $perKategori,
            ],
        ]);
    }
}
