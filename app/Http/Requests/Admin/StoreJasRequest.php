<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'nama' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'ukuran' => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9]+(\s*,\s*[A-Za-z0-9]+)*$/'],
            'harga_sewa' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'stok' => ['required', 'integer', 'min:0', 'max:10000'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_aktif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'ukuran.regex' => 'Format ukuran harus dipisahkan koma, contoh: S,M,L,XL.',
            'harga_sewa.min' => 'Harga sewa minimal Rp 1.000.',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_id' => 'kategori',
            'nama' => 'nama jas',
            'harga_sewa' => 'harga sewa',
            'is_aktif' => 'status aktif',
        ];
    }
}
