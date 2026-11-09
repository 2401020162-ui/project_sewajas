<?php

namespace App\Http\Requests;

use App\Models\Jas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreKeranjangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'jas_id' => ['required', 'integer', 'exists:jas,id'],
            'ukuran' => ['required', 'string', 'max:10'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:10'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $jas = Jas::find($this->integer('jas_id'));

            if (! $jas || ! $jas->is_aktif) {
                $v->errors()->add('jas_id', 'Jas tidak tersedia.');

                return;
            }

            if (! in_array($this->input('ukuran'), $jas->ukuran_list, true)) {
                $v->errors()->add('ukuran', 'Ukuran yang dipilih tidak tersedia untuk jas ini.');
            }

            $jumlahDiKeranjang = $this->user()->keranjang()
                ->where('jas_id', $jas->id)
                ->where('ukuran', $this->input('ukuran'))
                ->value('jumlah') ?? 0;

            if (($jumlahDiKeranjang + $this->integer('jumlah')) > $jas->stok) {
                $v->errors()->add('jumlah', "Stok tidak mencukupi. Sisa stok {$jas->stok}.");
            }
        });
    }

    public function attributes(): array
    {
        return [
            'jas_id' => 'jas',
            'ukuran' => 'ukuran',
            'jumlah' => 'jumlah',
            'tanggal_mulai' => 'tanggal mulai sewa',
            'tanggal_selesai' => 'tanggal selesai sewa',
        ];
    }
}
