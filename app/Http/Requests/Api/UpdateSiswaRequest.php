<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siswaId = $this->route('siswa')?->id_siswa;

        return [
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'nis' => ['sometimes', 'required', 'string', 'unique:siswa,nis,'.$siswaId.',id_siswa'],
            'kelas' => ['sometimes', 'required', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'angkatan' => ['nullable', 'string', 'max:10'],
        ];
    }
}
