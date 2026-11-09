<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TolakPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'catatan_admin' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_admin.required' => 'Catatan wajib diisi saat menolak pembayaran.',
            'catatan_admin.min' => 'Catatan minimal 5 karakter agar customer paham alasannya.',
        ];
    }
}
