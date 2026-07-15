<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePengembalianRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Kerusakan;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * POST /api/peminjaman/{peminjaman}/pengembalian  (admin, petugas)
     * Mencatat pengembalian alat: mengembalikan stok, dan bila kondisi
     * "rusak" otomatis mengubah kondisi alat + mencatat riwayat kerusakan.
     */
    public function store(StorePengembalianRequest $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'acc') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini belum disetujui atau sudah dikembalikan.',
            ], 422);
        }

        DB::transaction(function () use ($request, $peminjaman) {
            $pengembalian = Pengembalian::create([
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'kondisi' => $request->kondisi,
                'catatan' => $request->catatan,
            ]);

            $peminjaman->update(['status' => 'selesai']);
            $peminjaman->alat->increment('stok', $peminjaman->jumlah);

            if ($request->kondisi === 'rusak') {
                $peminjaman->alat->update(['kondisi' => 'rusak']);

                Kerusakan::create([
                    'id_pengembalian' => $pengembalian->id_pengembalian,
                    'id_alat' => $peminjaman->id_alat,
                    'deskripsi' => $request->deskripsi_kerusakan,
                    'tanggal' => $request->tanggal_kembali_aktual,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian alat berhasil dicatat.',
            'data' => new PeminjamanResource($peminjaman->fresh(['siswa', 'alat', 'pengembalian.kerusakan'])),
        ], 201);
    }
}
