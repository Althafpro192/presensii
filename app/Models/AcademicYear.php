<?php

namespace App\Models;

use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AcademicYear model – tracks school year periods.
 */
class AcademicYear extends Model
{
    use Tenantable;

    protected $fillable = ['school_id', 'name', 'start_date', 'end_date', 'is_current'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Set this as the current academic year and unset others.
     */
    public function markAsCurrent(): void
    {
        // Unset all others for this school
        static::withoutGlobalScopes()
            ->where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->update(['is_current' => true]);
    }
}
