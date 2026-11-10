<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case Qris = 'qris';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Qris => 'QRIS',
            self::Cash => 'Cash di Toko',
        };
    }

    public function butuhBukti(): bool
    {
        return $this === self::Qris;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
