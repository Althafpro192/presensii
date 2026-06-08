<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $classroom = $user->getHomeroomClassroom();

        if (!$classroom) {
            return Inertia::render('Teacher/Attendance/Index', [
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'students' => [],
                'date' => Carbon::today()->toDateString(),
            ]);
        }

        $date = $request->input('date', Carbon::today()->toDateString());

        $students = Student::withoutGlobalScopes()
            ->where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($date) {
                $attendance = Attendance::withoutGlobalScopes()
                    ->where('student_id', $student->id)
                    ->where('date', $date)
                    ->first();

                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'has_lost_card' => $student->hasLostCard(),
                    'attendance' => $attendance ? [
                        'id' => $attendance->id,
                        'status' => $attendance->status->value,
                        'status_label' => $attendance->status->label(),
                        'check_in_time' => $attendance->check_in_time,
                        'check_out_time' => $attendance->check_out_time,
                    ] : null,
                ];
            });

        return Inertia::render('Teacher/Attendance/Index', [
            'classroom' => $classroom,
            'students' => $students,
            'date' => $date,
            'statuses' => collect(AttendanceStatus::cases())->map(fn($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    /**
     * Manual attendance update by homeroom teacher (e.g., for lost card).
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa,terlambat',
            'check_in_time' => 'nullable|date_format:H:i:s',
        ]);

        $attendance = Attendance::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('date', $validated['date'])
            ->first();

        if ($attendance) {
            $attendance->update([
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? $attendance->check_in_time,
            ]);
        } else {
            Attendance::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'date' => $validated['date'],
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? now()->toTimeString(),
            ]);
        }

        return redirect()->back()->with('success', "Presensi {$student->name} berhasil diperbarui.");
    }
}
