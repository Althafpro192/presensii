<?php

namespace App\Services;

use App\Models\AcademicCalendar;
use App\Models\GovernmentHoliday;
use App\Models\School;
use App\Models\SchoolHoliday;
use Carbon\Carbon;

/**
 * Service to determine if a given date is a holiday for a specific school.
 *
 * Priority order:
 * 1. School override "masuk" (force school day) → NOT holiday
 * 2. School holiday "libur" → IS holiday
 * 3. Academic calendar "libur_semester" range → IS holiday
 * 4. Government/national holiday → IS holiday
 * 5. Weekend (configurable per school) → IS holiday
 * 6. Default → NOT holiday
 */
class HolidayService
{
    /**
     * Check if a date is a holiday for the given school.
     */
    public function isHoliday(Carbon|string $date, int $schoolId): bool
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // 1. Check school override "masuk" (force school day even on government holiday)
        $overrideMasuk = SchoolHoliday::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('event_date', $date->toDateString())
            ->where('type', 'masuk')
            ->where('override_government', true)
            ->exists();

        if ($overrideMasuk) {
            return false; // Forced school day
        }

        // 2. Check school-specific holiday
        $schoolHoliday = SchoolHoliday::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('event_date', $date->toDateString())
            ->where('type', 'libur')
            ->exists();

        if ($schoolHoliday) {
            return true;
        }

        // 3. Check academic calendar (libur_semester range)
        $inSemesterBreak = AcademicCalendar::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('type', 'libur_semester')
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->exists();

        if ($inSemesterBreak) {
            return true;
        }

        // 4. Check government/national holiday
        $govHoliday = GovernmentHoliday::where('holiday_date', $date->toDateString())
            ->exists();

        if ($govHoliday) {
            return true;
        }

        // 5. Check weekend (configurable per school)
        $school = School::find($schoolId);
        $weekendDays = $school?->getSetting('weekend_days', [0, 6]) ?? [0, 6]; // Sunday=0, Saturday=6

        if (in_array($date->dayOfWeek, $weekendDays)) {
            return true;
        }

        // 6. Default: not a holiday
        return false;
    }

    /**
     * Get all holidays for a given month/year for a school.
     * Returns array of dates with their holiday info.
     */
    public function getHolidaysForMonth(int $month, int $year, int $schoolId): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $holidays = [];

        // Get government holidays
        $govHolidays = GovernmentHoliday::whereBetween('holiday_date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($h) => $h->holiday_date->toDateString());

        // Get school holidays
        $schoolHolidays = SchoolHoliday::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($h) => $h->event_date->toDateString());

        // Get academic calendar events
        $calendarEvents = AcademicCalendar::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();

        $school = School::find($schoolId);
        $weekendDays = $school?->getSetting('weekend_days', [0, 6]) ?? [0, 6];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $isHoliday = false;
            $reason = null;
            $type = null;

            // Check school override masuk
            if (isset($schoolHolidays[$dateStr]) && $schoolHolidays[$dateStr]->type->value === 'masuk') {
                continue; // Forced school day, skip
            }

            if (isset($schoolHolidays[$dateStr]) && $schoolHolidays[$dateStr]->type->value === 'libur') {
                $isHoliday = true;
                $reason = $schoolHolidays[$dateStr]->name ?? 'Libur Sekolah';
                $type = 'school';
            } elseif (isset($govHolidays[$dateStr])) {
                $isHoliday = true;
                $reason = $govHolidays[$dateStr]->name;
                $type = 'government';
            } elseif (in_array($date->dayOfWeek, $weekendDays)) {
                $isHoliday = true;
                $reason = 'Akhir Pekan';
                $type = 'weekend';
            }

            // Check calendar events
            foreach ($calendarEvents as $event) {
                if ($date->between($event->start_date, $event->end_date)) {
                    if ($event->type->value === 'libur_semester') {
                        $isHoliday = true;
                        $reason = $event->name;
                        $type = 'semester_break';
                    }
                    break;
                }
            }

            if ($isHoliday) {
                $holidays[] = [
                    'date' => $dateStr,
                    'reason' => $reason,
                    'type' => $type,
                ];
            }
        }

        return $holidays;
    }
}
