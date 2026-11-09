<?php

namespace App\Http\Requests;

use App\Models\Keranjang;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateKeranjangRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Keranjang|null $keranjang */
        $keranjang = $this->route('keranjang');

        return $keranjang !== null && $keranjang->user_id === $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            /** @var Keranjang|null $keranjang */
            $keranjang = $this->route('keranjang');

            if (! $keranjang || $v->errors()->isNotEmpty()) {
                return;
            }

            if ($this->integer('jumlah') > $keranjang->jas->stok) {
                $v->errors()->add('jumlah', "Stok tidak mencukupi. Sisa stok {$keranjang->jas->stok}.");
            }
        });
    }
}
