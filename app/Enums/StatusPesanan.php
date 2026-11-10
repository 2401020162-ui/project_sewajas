<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case Disewa = 'disewa';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::Disewa => 'Disewa',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::MenungguVerifikasi => 'info',
            self::Disewa => 'primary',
            self::Selesai => 'success',
            self::Dibatalkan => 'danger',
        };
    }

    /** Status yang stoknya masih "terpakai" (belum kembali). */
    public function stokTerpakai(): bool
    {
        return in_array($this, [self::Pending, self::MenungguVerifikasi, self::Disewa], true);
    }

    /** Status yang masih boleh dibatalkan customer/admin (belum Disewa). */
    public function bisaDibatalkan(): bool
    {
        return in_array($this, [self::Pending, self::MenungguVerifikasi], true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
