<?php

namespace App\Models;

use App\Enums\PromotionStatus;
use App\Models\Traits\Tenantable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Student model – core entity for attendance tracking.
 */
class Student extends Model
{
    use Tenantable, Auditable, SoftDeletes;

    protected $fillable = [
        'school_id', 'classroom_id', 'nis', 'name', 'rfid',
        'parent_phone', 'promotion_status', 'graduated_at',
    ];

    protected function casts(): array
    {
        return [
            'promotion_status' => PromotionStatus::class,
            'graduated_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function cardAssignments(): HasMany
    {
        return $this->hasMany(RfidCardAssignment::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function promotionLogs(): HasMany
    {
        return $this->hasMany(PromotionLog::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Get the currently active RFID card assignment.
     */
    public function activeCard(): ?RfidCardAssignment
    {
        return $this->cardAssignments()
            ->where('status', 'active')
            ->latest('assigned_at')
            ->first();
    }

    /**
     * Check if student has a lost card (no active card).
     */
    public function hasLostCard(): bool
    {
        return $this->cardAssignments()->where('status', 'lost')->exists()
            && !$this->cardAssignments()->where('status', 'active')->exists();
    }
}
