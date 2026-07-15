<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pengembalian' => $this->id_pengembalian,
            'id_peminjaman' => $this->id_peminjaman,
            'tanggal_kembali_aktual' => $this->tanggal_kembali_aktual?->toDateString(),
            'kondisi' => $this->kondisi,
            'catatan' => $this->catatan,
            'kerusakan' => new KerusakanResource($this->whenLoaded('kerusakan')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
