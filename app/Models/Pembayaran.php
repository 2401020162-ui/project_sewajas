<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'metode',
        'bukti_pembayaran',
        'status',
        'tanggal_bayar',
        'tanggal_verifikasi',
        'diverifikasi_oleh',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'metode' => MetodePembayaran::class,
            'status' => StatusPembayaran::class,
            'tanggal_bayar' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_pembayaran ? asset('storage/'.$this->bukti_pembayaran) : null;
    }
}
