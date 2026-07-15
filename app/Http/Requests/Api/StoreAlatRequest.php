<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi role sudah ditangani middleware 'api.role:admin'
    }

    public function rules(): array
    {
        return [
            'nama_alat' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:baik,rusak'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
