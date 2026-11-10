<?php

namespace App\Http\Requests;

use App\Enums\MetodePembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'metode' => ['required', Rule::enum(MetodePembayaran::class)],
            'bukti_pembayaran' => [
                'nullable',
                'required_if:metode,qris',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'tanggal_bayar' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_pembayaran.required_if' => 'Bukti pembayaran wajib diunggah untuk metode QRIS.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar.',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'metode' => 'metode pembayaran',
            'bukti_pembayaran' => 'bukti pembayaran',
            'tanggal_bayar' => 'tanggal bayar',
        ];
    }
}
