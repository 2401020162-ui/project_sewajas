<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nomor_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isCustomer(): bool
    {
        return $this->role === Role::Customer;
    }

    /** Pesanan milik customer. */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class)->latest();
    }

    public function keranjang(): HasMany
    {
        return $this->hasMany(Keranjang::class);
    }

    public function testimoni(): HasMany
    {
        return $this->hasMany(Testimoni::class);
    }

    /** Pesanan yang diverifikasi/ditangani oleh admin ini. */
    public function pesananDitangani(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'admin_id');
    }

    public function pembayaranDiverifikasi(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'diverifikasi_oleh');
    }
}
