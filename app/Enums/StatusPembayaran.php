<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case Pending = 'pending';
    case Diverifikasi = 'diverifikasi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Diverifikasi => 'Lunas',
            self::Ditolak => 'Ditolak',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Diverifikasi => 'success',
            self::Ditolak => 'danger',
        };
    }
}
