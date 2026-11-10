<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'jas_id',
        'admin_id',
        'nama_pemesan',
        'nomor_hp',
        'tanggal_pesan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_dikembalikan',
        'ukuran',
        'jumlah',
        'total_harga',
        'status',
        'catatan',
        'catatan_pengembalian',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesan' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_dikembalikan' => 'date',
            'jumlah' => 'integer',
            'total_harga' => 'integer',
            'status' => StatusPesanan::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pesanan $pesanan) {
            if (blank($pesanan->kode_pesanan)) {
                $pesanan->kode_pesanan = static::buatKode();
            }
        });
    }

    public static function buatKode(): string
    {
        do {
            $kode = 'PJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (static::where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jas(): BelongsTo
    {
        return $this->belongsTo(Jas::class, 'jas_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }

    public function getLamaSewaAttribute(): int
    {
        return max(1, (int) Carbon::parse($this->tanggal_mulai)->diffInDays(Carbon::parse($this->tanggal_selesai)));
    }

    /** Label status pesanan sesuai alur proposal. */
    public function getStatusTampilAttribute(): string
    {
        return $this->status->label();
    }

    public function getBadgeTampilAttribute(): string
    {
        return $this->status->badge();
    }

    /** Nama pemesan yang diisi saat checkout (fallback ke akun). */
    public function getNamaPemesanTampilAttribute(): string
    {
        return $this->nama_pemesan ?: $this->user?->name ?? '-';
    }

    /** Nomor HP pemesan yang diisi saat checkout (fallback ke akun). */
    public function getNomorHpTampilAttribute(): string
    {
        return $this->nomor_hp ?: $this->user?->nomor_hp ?? '-';
    }
}
