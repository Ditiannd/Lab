<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSiswaRequest;
use App\Http\Requests\Api\UpdateSiswaRequest;
use App\Http\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    /**
     * GET /api/siswa  (admin)
     */
    public function index(Request $request)
    {
        $siswa = Siswa::with('user')
            ->when($request->q, fn ($q, $s) => $q->where('nama', 'like', "%$s%")->orWhere('nis', 'like', "%$s%"))
            ->when($request->jurusan, fn ($q, $j) => $q->where('jurusan', $j))
            ->when($request->kelas, fn ($q, $k) => $q->where('kelas', $k))
            ->latest()
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return SiswaResource::collection($siswa)->additional(['success' => true]);
    }

    /**
     * POST /api/siswa  (admin)
     * Membuat akun siswa sekaligus data profil siswa (1 transaksi).
     */
    public function store(StoreSiswaRequest $request)
    {
        $siswa = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
            ]);

            return Siswa::create([
                'nama' => $request->nama,
                'nis' => $request->nis,
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
                'angkatan' => $request->angkatan,
                'id_user' => $user->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Siswa '{$siswa->nama}' berhasil ditambahkan.",
            'data' => new SiswaResource($siswa->load('user')),
        ], 201);
    }

    /**
     * GET /api/siswa/{siswa}  (admin)
     */
    public function show(Siswa $siswa)
    {
        return response()->json([
            'success' => true,
            'data' => new SiswaResource($siswa->load('user')),
        ]);
    }

    /**
     * PUT/PATCH /api/siswa/{siswa}  (admin)
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa)
    {
        $siswa->update($request->safe()->only(['nama', 'nis', 'kelas', 'jurusan', 'angkatan']));

        if ($siswa->user && $request->filled('nama')) {
            $siswa->user->update(['name' => $request->nama]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => new SiswaResource($siswa->fresh('user')),
        ]);
    }

    /**
     * DELETE /api/siswa/{siswa}  (admin)
     * Menghapus siswa sekaligus akun user terkait.
     */
    public function destroy(Siswa $siswa)
    {
        DB::transaction(function () use ($siswa) {
            $user = $siswa->user;
            $siswa->delete();
            $user?->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus.',
        ]);
    }
}
