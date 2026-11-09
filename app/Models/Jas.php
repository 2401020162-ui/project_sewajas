<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jas extends Model
{
    protected $table = 'jas';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'ukuran',
        'harga_sewa',
        'stok',
        'gambar',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_sewa' => 'integer',
            'stok' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'jas_id');
    }

    public function testimoni(): HasMany
    {
        return $this->hasMany(Testimoni::class, 'jas_id');
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'jas_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        return $query->when($kata, function (Builder $q) use ($kata) {
            $q->where(function (Builder $w) use ($kata) {
                $w->where('nama', 'like', "%{$kata}%")
                    ->orWhere('deskripsi', 'like', "%{$kata}%");
            });
        });
    }

    /** @return array<int, string> */
    public function getUkuranListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $this->ukuran))));
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/'.$this->gambar) : null;
    }

    public function getRataRatingAttribute(): float
    {
        return round((float) ($this->testimoni_avg_rating ?? $this->testimoni()->avg('rating') ?? 0), 1);
    }
}
