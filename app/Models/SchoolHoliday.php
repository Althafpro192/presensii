<?php

namespace App\Models;

use App\Enums\SchoolHolidayType;
use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SchoolHoliday model – school-specific holidays with government override.
 */
class SchoolHoliday extends Model
{
    use Tenantable;

    protected $fillable = [
        'school_id', 'event_date', 'type', 'name',
        'override_government', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'type' => SchoolHolidayType::class,
            'override_government' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
