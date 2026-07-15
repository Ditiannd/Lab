<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_alat' => ['sometimes', 'required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'stok' => ['sometimes', 'required', 'integer', 'min:0'],
            'kondisi' => ['sometimes', 'required', 'in:baik,rusak'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
