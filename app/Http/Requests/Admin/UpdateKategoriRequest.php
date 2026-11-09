<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama')->ignore($this->route('kategori')),
            ],
        ];
    }

    public function messages(): array
    {
        return ['nama.unique' => 'Nama kategori sudah ada.'];
    }
}
