<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $fillable = [
        'user_id',
        'jas_id',
        'ukuran',
        'jumlah',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jas(): BelongsTo
    {
        return $this->belongsTo(Jas::class, 'jas_id');
    }

    public function getLamaSewaAttribute(): int
    {
        return max(1, (int) Carbon::parse($this->tanggal_mulai)->diffInDays(Carbon::parse($this->tanggal_selesai)));
    }

    public function getSubtotalAttribute(): int
    {
        return (int) ($this->jas->harga_sewa * $this->jumlah * $this->lama_sewa);
    }
}
