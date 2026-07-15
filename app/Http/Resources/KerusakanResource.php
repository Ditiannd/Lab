<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KerusakanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kerusakan' => $this->id_kerusakan,
            'id_pengembalian' => $this->id_pengembalian,
            'id_alat' => $this->id_alat,
            'deskripsi' => $this->deskripsi,
            'tanggal' => $this->tanggal?->toDateString(),
            'alat' => new AlatResource($this->whenLoaded('alat')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
