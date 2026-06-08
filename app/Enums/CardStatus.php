<?php

namespace App\Enums;

enum CardStatus: string
{
    case Active = 'active';
    case Lost = 'lost';
    case Damaged = 'damaged';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Lost => 'Hilang',
            self::Damaged => 'Rusak',
            self::Inactive => 'Nonaktif',
        };
    }
}
