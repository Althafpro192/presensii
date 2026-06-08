<?php

namespace App\Enums;

enum SchoolHolidayType: string
{
    case Libur = 'libur';
    case Masuk = 'masuk';

    public function label(): string
    {
        return match ($this) {
            self::Libur => 'Libur',
            self::Masuk => 'Masuk (Override)',
        };
    }
}
