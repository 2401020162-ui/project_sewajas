<?php

namespace App\Http\Requests;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use App\Models\Testimoni;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTestimoniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'jas_id' => ['required', 'integer', 'exists:jas,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'komentar' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $sudahSewa = Pesanan::where('user_id', $this->user()->id)
                ->where('jas_id', $this->integer('jas_id'))
                ->where('status', StatusPesanan::Selesai)
                ->exists();

            if (! $sudahSewa) {
                $v->errors()->add('jas_id', 'Testimoni hanya dapat ditulis setelah pesanan jas ini selesai.');
            }

            $sudahUlas = Testimoni::where('user_id', $this->user()->id)
                ->where('jas_id', $this->integer('jas_id'))
                ->exists();

            if ($sudahUlas) {
                $v->errors()->add('jas_id', 'Kamu sudah memberi testimoni untuk jas ini.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'jas_id' => 'jas',
            'rating' => 'rating',
            'komentar' => 'komentar',
        ];
    }
}
