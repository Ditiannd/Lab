<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePeminjamanRequest;
use App\Http\Requests\Api\TolakPeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * GET /api/peminjaman
     * - admin & petugas: lihat semua pengajuan (filter status opsional).
     * - siswa: otomatis hanya melihat riwayat miliknya sendiri.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Peminjaman::with(['siswa', 'alat', 'petugas', 'pengembalian']);

        if ($user->role === 'siswa') {
            $siswa = Siswa::where('id_user', $user->id)->firstOrFail();
            $query->where('id_siswa', $siswa->id_siswa);
        }

        $peminjaman = $query
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return PeminjamanResource::collection($peminjaman)->additional(['success' => true]);
    }

    /**
     * POST /api/peminjaman  (siswa)
     * Mengajukan peminjaman alat baru. Status awal selalu "pending".
     */
    public function store(StorePeminjamanRequest $request)
    {
        $siswa = Siswa::where('id_user', Auth::id())->first();

        if (! $siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data siswa.',
            ], 422);
        }

        $alat = Alat::findOrFail($request->id_alat);

        if ($alat->kondisi === 'rusak') {
            return response()->json([
                'success' => false,
                'message' => 'Alat ini sedang dalam kondisi rusak dan tidak dapat dipinjam.',
            ], 422);
        }

        if ($request->jumlah > $alat->stok) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$alat->stok} unit.",
            ], 422);
        }

        $aktif = Peminjaman::where('id_siswa', $siswa->id_siswa)
            ->where('id_alat', $request->id_alat)
            ->whereIn('status', ['pending', 'acc'])
            ->exists();

        if ($aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki peminjaman aktif untuk alat ini.',
            ], 422);
        }

        $peminjaman = Peminjaman::create([
            'id_siswa' => $siswa->id_siswa,
            'id_alat' => $request->id_alat,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'jumlah' => $request->jumlah,
            'status' => 'pending',
            'catatan_siswa' => $request->catatan_siswa,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman berhasil dikirim. Menunggu validasi petugas.',
            'data' => new PeminjamanResource($peminjaman->load(['alat', 'siswa'])),
        ], 201);
    }

    /**
     * GET /api/peminjaman/{peminjaman}
     */
    public function show(Peminjaman $peminjaman)
    {
        $user = Auth::user();

        if ($user->role === 'siswa' && $peminjaman->siswa->id_user !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak melihat data peminjaman ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new PeminjamanResource($peminjaman->load(['siswa', 'alat', 'petugas', 'pengembalian.kerusakan'])),
        ]);
    }

    /**
     * PATCH /api/peminjaman/{peminjaman}/acc  (admin, petugas)
     * Menyetujui pengajuan & mengurangi stok alat (dalam DB transaction + row lock).
     */
    public function acc(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diproses sebelumnya.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($peminjaman) {
                $alat = Alat::lockForUpdate()->findOrFail($peminjaman->id_alat);

                if ($peminjaman->jumlah > $alat->stok) {
                    throw new \RuntimeException("Stok tidak mencukupi untuk menyetujui peminjaman ini. Stok: {$alat->stok}");
                }

                $alat->decrement('stok', $peminjaman->jumlah);

                $peminjaman->update([
                    'status' => 'acc',
                    'id_petugas' => Auth::id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disetujui dan stok alat telah dikurangi.',
            'data' => new PeminjamanResource($peminjaman->fresh(['siswa', 'alat', 'petugas'])),
        ]);
    }

    /**
     * PATCH /api/peminjaman/{peminjaman}/tolak  (admin, petugas)
     */
    public function tolak(TolakPeminjamanRequest $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diproses sebelumnya.',
            ], 422);
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'id_petugas' => Auth::id(),
            'catatan_petugas' => $request->catatan_petugas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman telah ditolak.',
            'data' => new PeminjamanResource($peminjaman->fresh(['siswa', 'alat', 'petugas'])),
        ]);
    }
}
