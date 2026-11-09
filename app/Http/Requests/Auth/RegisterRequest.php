<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'nomor_hp' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah terdaftar. Silakan masuk.',
            'nomor_hp.regex' => 'Nomor HP harus diawali 08 dan terdiri dari 10–15 digit.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'nomor_hp' => 'nomor HP',
            'password' => 'kata sandi',
        ];
    }
}
