<?php

namespace App\Models;

use App\Enums\CalendarEventType;
use App\Models\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AcademicCalendar model – date ranges for semester breaks, exams, promotions.
 */
class AcademicCalendar extends Model
{
    use Tenantable;

    protected $fillable = ['school_id', 'name', 'start_date', 'end_date', 'type'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'type' => CalendarEventType::class,
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if a given date falls within this calendar event.
     */
    public function containsDate(\Carbon\Carbon $date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }
}
