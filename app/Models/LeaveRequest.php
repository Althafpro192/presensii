<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveType;
use App\Models\Traits\Tenantable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LeaveRequest model – student leave/sick request from parents.
 */
class LeaveRequest extends Model
{
    use Tenantable, Auditable;

    protected $fillable = [
        'school_id', 'student_id', 'parent_id', 'date', 'type',
        'reason', 'attachment', 'status', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => LeaveType::class,
            'status' => LeaveRequestStatus::class,
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
