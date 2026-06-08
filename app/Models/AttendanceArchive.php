<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AttendanceArchive model – archived attendance records older than 1 year.
 */
class AttendanceArchive extends Model
{
    protected $table = 'attendance_archive';

    protected $fillable = [
        'school_id', 'student_id', 'date', 'check_in_time',
        'check_out_time', 'status', 'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'archived_at' => 'datetime',
        ];
    }
}
