<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'tanggal_awal' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_awal'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $awal = $this->input('tanggal_awal');
            $akhir = $this->input('tanggal_akhir');

            if (($awal && ! $akhir) || (! $awal && $akhir)) {
                $v->errors()->add('tanggal_awal', 'Isi tanggal awal dan tanggal akhir sekaligus.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'tanggal_awal' => 'tanggal awal',
            'tanggal_akhir' => 'tanggal akhir',
        ];
    }
}
