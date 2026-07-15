<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAlatRequest;
use App\Http\Requests\Api\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * GET /api/alat
     * List alat + filter (q, kategori, kondisi) + pagination.
     * Semua role yang login (admin, petugas, siswa) dapat mengakses.
     */
    public function index(Request $request)
    {
        $alat = Alat::query()
            ->when($request->q, fn ($q, $s) => $q->where('nama_alat', 'like', "%$s%")->orWhere('lokasi', 'like', "%$s%"))
            ->when($request->kategori, fn ($q, $k) => $q->where('kategori', $k))
            ->when($request->kondisi, fn ($q, $k) => $q->where('kondisi', $k))
            ->when($request->boolean('tersedia'), fn ($q) => $q->where('kondisi', 'baik')->where('stok', '>', 0))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return AlatResource::collection($alat)->additional(['success' => true]);
    }

    /**
     * POST /api/alat  (admin)
     */
    public function store(StoreAlatRequest $request)
    {
        $data = $request->safe()->only(['nama_alat', 'kategori', 'stok', 'kondisi', 'lokasi', 'deskripsi']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alat', 'public');
        }

        $alat = Alat::create($data);

        return response()->json([
            'success' => true,
            'message' => "Alat '{$alat->nama_alat}' berhasil ditambahkan.",
            'data' => new AlatResource($alat),
        ], 201);
    }

    /**
     * GET /api/alat/{alat}
     */
    public function show(Alat $alat)
    {
        return response()->json([
            'success' => true,
            'data' => new AlatResource($alat->load('kerusakan')),
        ]);
    }

    /**
     * PUT/PATCH /api/alat/{alat}  (admin)
     */
    public function update(UpdateAlatRequest $request, Alat $alat)
    {
        $data = $request->safe()->only(['nama_alat', 'kategori', 'stok', 'kondisi', 'lokasi', 'deskripsi']);

        if ($request->hasFile('foto')) {
            if ($alat->foto) {
                Storage::disk('public')->delete($alat->foto);
            }
            $data['foto'] = $request->file('foto')->store('alat', 'public');
        }

        $alat->update($data);

        return response()->json([
            'success' => true,
            'message' => "Alat '{$alat->nama_alat}' berhasil diperbarui.",
            'data' => new AlatResource($alat),
        ]);
    }

    /**
     * DELETE /api/alat/{alat}  (admin)
     */
    public function destroy(Alat $alat)
    {
        if ($alat->foto) {
            Storage::disk('public')->delete($alat->foto);
        }
        $alat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil dihapus.',
        ]);
    }
}
