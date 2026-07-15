<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_kembali_aktual' => ['required', 'date'],
            'kondisi' => ['required', 'in:baik,rusak'],
            'deskripsi_kerusakan' => ['nullable', 'required_if:kondisi,rusak', 'string'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
