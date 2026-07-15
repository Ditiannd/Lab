<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_siswa' => $this->id_siswa,
            'nama' => $this->nama,
            'nis' => $this->nis,
            'kelas' => $this->kelas,
            'jurusan' => $this->jurusan,
            'angkatan' => $this->angkatan,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
