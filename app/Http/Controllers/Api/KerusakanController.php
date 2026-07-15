<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KerusakanResource;
use App\Models\Kerusakan;
use Illuminate\Http\Request;

class KerusakanController extends Controller
{
    /**
     * GET /api/kerusakan  (admin, petugas)
     * Riwayat kerusakan alat, terbaru lebih dulu.
     */
    public function index(Request $request)
    {
        $kerusakan = Kerusakan::with(['alat', 'pengembalian.peminjaman.siswa'])
            ->when($request->id_alat, fn ($q, $id) => $q->where('id_alat', $id))
            ->latest('tanggal')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return KerusakanResource::collection($kerusakan)->additional(['success' => true]);
    }
}
