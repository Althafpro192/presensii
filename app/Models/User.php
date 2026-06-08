<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User model – supports multiple roles across schools.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property int|null $school_id
 * @property int|null $classroom_id
 * @property string|null $phone
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'school_id',
        'classroom_id', 'phone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
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

    /**
     * Students linked to this parent user via pivot.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withTimestamps();
    }

    /**
     * Classroom teacher assignments for this teacher.
     */
    public function classroomTeacherAssignments(): HasMany
    {
        return $this->hasMany(ClassroomTeacher::class, 'teacher_id');
    }

    /**
     * Leave requests approved by this user.
     */
    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    // ── Role Helpers ───────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isAdminSekolah(): bool
    {
        return $this->role === UserRole::AdminSekolah;
    }

    public function isKurikulum(): bool
    {
        return $this->role === UserRole::Kurikulum;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    public function isOrangTua(): bool
    {
        return $this->role === UserRole::OrangTua;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the homeroom classroom for current academic year (for teachers).
     */
    public function getHomeroomClassroom(): ?Classroom
    {
        if (!$this->isTeacher()) {
            return null;
        }

        $currentYear = AcademicYear::where('school_id', $this->school_id)
            ->where('is_current', true)
            ->first();

        if (!$currentYear) {
            return null;
        }

        $assignment = ClassroomTeacher::where('teacher_id', $this->id)
            ->where('academic_year_id', $currentYear->id)
            ->first();

        return $assignment?->classroom;
    }
}
