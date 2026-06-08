<?php

namespace App\Models;

use App\Enums\SanctionType;
use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentViolation model – toggleable per school.
 */
class StudentViolation extends Model
{
    use Tenantable;

    protected $fillable = [
        'school_id', 'student_id', 'recorded_by', 'violation_type',
        'description', 'violation_date', 'sanction',
    ];

    protected function casts(): array
    {
        return [
            'violation_date' => 'date',
            'sanction' => SanctionType::class,
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

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
