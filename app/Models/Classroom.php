<?php

namespace App\Models;

use App\Models\Traits\Tenantable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classroom model – belongs to a school, has students and teacher assignments.
 */
class Classroom extends Model
{
    use Tenantable, Auditable;

    protected $fillable = ['school_id', 'name', 'grade_level', 'order'];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'order' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(ClassroomTeacher::class);
    }

    /**
     * Get the current homeroom teacher for this classroom.
     */
    public function currentTeacher(): ?User
    {
        $currentYear = AcademicYear::withoutGlobalScopes()
            ->where('school_id', $this->school_id)
            ->where('is_current', true)
            ->first();

        if (!$currentYear) {
            return null;
        }

        $assignment = $this->teacherAssignments()
            ->where('academic_year_id', $currentYear->id)
            ->first();

        return $assignment?->teacher;
    }
}
