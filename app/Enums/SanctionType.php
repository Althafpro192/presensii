<?php

namespace App\Enums;

enum SanctionType: string
{
    case Peringatan = 'peringatan';
    case Skors = 'skors';
    case DikembalikanKeOrtu = 'dikembalikan_ke_ortu';

    public function label(): string
    {
        return match ($this) {
            self::Peringatan => 'Peringatan',
            self::Skors => 'Skorsing',
            self::DikembalikanKeOrtu => 'Dikembalikan ke Orang Tua',
        };
    }
}
