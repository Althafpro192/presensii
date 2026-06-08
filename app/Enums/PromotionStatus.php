<?php

namespace App\Enums;

enum PromotionStatus: string
{
    case Pending = 'pending';
    case Naik = 'naik';
    case TidakNaik = 'tidak_naik';
    case Lulus = 'lulus';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Naik => 'Naik Kelas',
            self::TidakNaik => 'Tidak Naik',
            self::Lulus => 'Lulus',
        };
    }
}
