<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AlatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_alat' => $this->id_alat,
            'nama_alat' => $this->nama_alat,
            'kategori' => $this->kategori,
            'stok' => $this->stok,
            'kondisi' => $this->kondisi,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'foto' => $this->foto,
            'foto_url' => $this->foto ? Storage::disk('public')->url($this->foto) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
