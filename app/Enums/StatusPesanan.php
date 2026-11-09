<?php

namespace App\Enums;

enum StatusPesanan: string
{
    case Pending = 'pending';
    case Disetujui = 'disetujui';
    case Dikirim = 'dikirim';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Disetujui => 'Pembayaran Disetujui',
            self::Dikirim => 'Sedang Disewa',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Disetujui => 'info',
            self::Dikirim => 'primary',
            self::Selesai => 'success',
            self::Dibatalkan => 'danger',
        };
    }

    /** Status yang stoknya masih "terpakai" (belum kembali). */
    public function stokTerpakai(): bool
    {
        return in_array($this, [self::Pending, self::Disetujui, self::Dikirim], true);
    }

    /** Status yang masih boleh dibatalkan customer/admin. */
    public function bisaDibatalkan(): bool
    {
        return in_array($this, [self::Pending, self::Disetujui], true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
