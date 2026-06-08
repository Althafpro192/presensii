<?php

namespace App\Enums;

/**
 * Enum representing attendance status options.
 */
enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpa = 'alpa';
    case Terlambat = 'terlambat';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Alpa => 'Alpa',
            self::Terlambat => 'Terlambat',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Hadir => '#22c55e',
            self::Izin => '#3b82f6',
            self::Sakit => '#f59e0b',
            self::Alpa => '#ef4444',
            self::Terlambat => '#f97316',
        };
    }
}
