<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100', 'unique:kategori,nama'],
        ];
    }

    public function messages(): array
    {
        return ['nama.unique' => 'Nama kategori sudah ada.'];
    }
}
