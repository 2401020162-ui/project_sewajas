<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (Kategori $kategori) {
            if (blank($kategori->slug)) {
                $kategori->slug = static::slugUnik($kategori->nama);
            }
        });

        static::updating(function (Kategori $kategori) {
            if ($kategori->isDirty('nama')) {
                $kategori->slug = static::slugUnik($kategori->nama, $kategori->id);
            }
        });
    }

    public static function slugUnik(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 2;

        while (static::where('slug', $slug)->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))->exists()) {
            $slug = $dasar.'-'.$i++;
        }

        return $slug;
    }

    public function jas(): HasMany
    {
        return $this->hasMany(Jas::class, 'kategori_id');
    }
}
