<?php

namespace App\Enums;

enum CalendarEventType: string
{
    case LiburSemester = 'libur_semester';
    case Uts = 'uts';
    case Uas = 'uas';
    case Psb = 'psb';
    case KenaikanKelas = 'kenaikan_kelas';

    public function label(): string
    {
        return match ($this) {
            self::LiburSemester => 'Libur Semester',
            self::Uts => 'UTS',
            self::Uas => 'UAS',
            self::Psb => 'PSB',
            self::KenaikanKelas => 'Kenaikan Kelas',
        };
    }
}
