<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_peminjaman' => $this->id_peminjaman,
            'id_siswa' => $this->id_siswa,
            'id_alat' => $this->id_alat,
            'id_petugas' => $this->id_petugas,
            'tanggal_pinjam' => $this->tanggal_pinjam?->toDateString(),
            'tanggal_kembali' => $this->tanggal_kembali?->toDateString(),
            'jumlah' => $this->jumlah,
            'status' => $this->status,
            'catatan_siswa' => $this->catatan_siswa,
            'catatan_petugas' => $this->catatan_petugas,
            'siswa' => new SiswaResource($this->whenLoaded('siswa')),
            'alat' => new AlatResource($this->whenLoaded('alat')),
            'petugas' => new UserResource($this->whenLoaded('petugas')),
            'pengembalian' => new PengembalianResource($this->whenLoaded('pengembalian')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
