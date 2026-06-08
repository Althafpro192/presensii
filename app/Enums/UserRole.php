<?php

namespace App\Enums;

/**
 * Enum representing all user roles in the multi-school system.
 */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case AdminSekolah = 'admin_sekolah';
    case Kurikulum = 'kurikulum';
    case Teacher = 'teacher';
    case OrangTua = 'orang_tua';

    /**
     * Get a human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::AdminSekolah => 'Admin Sekolah',
            self::Kurikulum => 'Kurikulum',
            self::Teacher => 'Guru',
            self::OrangTua => 'Orang Tua',
        };
    }

    /**
     * Check if this role belongs to a specific school.
     */
    public function isSchoolLevel(): bool
    {
        return $this !== self::SuperAdmin;
    }
}
