<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case Transfer = 'transfer';
    case Qris = 'qris';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer Bank',
            self::Qris => 'QRIS',
            self::Cash => 'Cash di Toko',
        };
    }

    public function butuhBukti(): bool
    {
        return in_array($this, [self::Transfer, self::Qris], true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
