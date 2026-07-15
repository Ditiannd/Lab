<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_alat' => ['required', 'exists:alat,id_alat'],
            'tanggal_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali' => ['required', 'date', 'after:tanggal_pinjam'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan_siswa' => ['nullable', 'string', 'max:500'],
        ];
    }
}
