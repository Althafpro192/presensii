<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Models\Traits\Tenantable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Attendance model – daily attendance record per student.
 */
class Attendance extends Model
{
    use Tenantable, Auditable;

    protected $fillable = [
        'school_id', 'student_id', 'date', 'check_in_time',
        'check_out_time', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => AttendanceStatus::class,
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
