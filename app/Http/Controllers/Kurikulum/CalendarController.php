<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use App\Models\GovernmentHoliday;
use App\Models\SchoolHoliday;
use App\Services\HolidayService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(
        protected HolidayService $holidayService,
    ) {}

    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $governmentHolidays = GovernmentHoliday::where('year', $year)->get();
        $schoolHolidays = SchoolHoliday::where('school_id', $schoolId)
            ->whereYear('event_date', $year)
            ->get();
        $academicCalendars = AcademicCalendar::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->get();

        return Inertia::render('Kurikulum/Calendar/Index', [
            'governmentHolidays' => $governmentHolidays,
            'schoolHolidays' => $schoolHolidays,
            'academicCalendars' => $academicCalendars,
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'type' => 'required|in:libur,masuk',
            'name' => 'nullable|string|max:255',
            'override_government' => 'boolean',
        ]);

        SchoolHoliday::create([
            'school_id' => $request->user()->school_id,
            'event_date' => $validated['event_date'],
            'type' => $validated['type'],
            'name' => $validated['name'],
            'override_government' => $validated['override_government'] ?? false,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Kalender sekolah berhasil diperbarui.');
    }

    public function storeCalendarEvent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:libur_semester,uts,uas,psb,kenaikan_kelas',
        ]);

        AcademicCalendar::create([
            'school_id' => $request->user()->school_id,
            ...$validated,
        ]);

        return redirect()->back()->with('success', 'Event kalender berhasil ditambahkan.');
    }

    public function destroyHoliday(SchoolHoliday $holiday)
    {
        $holiday->delete();
        return redirect()->back()->with('success', 'Libur berhasil dihapus.');
    }
}
