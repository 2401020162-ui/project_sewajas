<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama_pemesan' => ['required', 'string', 'max:100'],
            'nomor_hp' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_hp.regex' => 'Nomor HP harus diawali 08 dan terdiri dari 10–15 digit.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_pemesan' => 'nama pemesan',
            'nomor_hp' => 'nomor HP',
            'catatan' => 'catatan',
        ];
    }
}
