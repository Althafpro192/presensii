<?php

namespace App\Enums;

enum RfidWriteStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Success = 'success';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Processing => 'Diproses',
            self::Success => 'Berhasil',
            self::Failed => 'Gagal',
        };
    }
}
