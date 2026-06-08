<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;

/**
 * Service for recording and managing attendance.
 */
class AttendanceService
{
    public function __construct(
        protected HolidayService $holidayService,
    ) {}

    /**
     * Record an RFID scan. Determines check-in vs check-out and status.
     *
     * @return array{success: bool, message: string, attendance?: Attendance}
     */
    public function recordScan(string $cardUid, int $schoolId): array
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if today is a holiday
        if ($this->holidayService->isHoliday($today, $schoolId)) {
            return [
                'success' => false,
                'message' => 'Hari libur. Presensi tidak dicatat.',
            ];
        }

        // Find student by RFID UID
        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('rfid', $cardUid)
            ->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Kartu tidak terdaftar.',
            ];
        }

        // Get school settings
        $school = School::find($schoolId);
        $lateThreshold = $school->getLateThreshold();

        // Check for existing attendance today
        $attendance = Attendance::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('date', $today->toDateString())
            ->first();

        if (!$attendance) {
            // First scan = check-in
            $status = AttendanceStatus::Hadir;

            // Check if late
            $thresholdTime = Carbon::parse($lateThreshold);
            if ($now->gt($thresholdTime)) {
                $status = AttendanceStatus::Terlambat;
            }

            $attendance = Attendance::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'date' => $today->toDateString(),
                'check_in_time' => $now->toTimeString(),
                'status' => $status,
            ]);

            return [
                'success' => true,
                'message' => "Check-in berhasil. {$student->name} - {$status->label()}",
                'attendance' => $attendance,
                'student_name' => $student->name,
                'type' => 'check_in',
            ];
        }

        if ($attendance->check_out_time) {
            return [
                'success' => false,
                'message' => "{$student->name} sudah check-out hari ini.",
            ];
        }

        // Second scan = check-out
        $attendance->update([
            'check_out_time' => $now->toTimeString(),
        ]);

        return [
            'success' => true,
            'message' => "Check-out berhasil. {$student->name}",
            'attendance' => $attendance,
            'student_name' => $student->name,
            'type' => 'check_out',
        ];
    }

    /**
     * Mark students who haven't checked in by the absent threshold as "alpa".
     */
    public function markAbsentStudents(int $schoolId): int
    {
        $today = Carbon::today();
        $school = School::find($schoolId);

        if (!$school || $this->holidayService->isHoliday($today, $schoolId)) {
            return 0;
        }

        // Get all students who don't have attendance today
        $studentsWithAttendance = Attendance::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('date', $today->toDateString())
            ->pluck('student_id');

        $absentStudents = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereNotNull('classroom_id') // Only active students
            ->whereNotIn('id', $studentsWithAttendance)
            ->get();

        $count = 0;
        foreach ($absentStudents as $student) {
            Attendance::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'date' => $today->toDateString(),
                'status' => AttendanceStatus::Alpa,
            ]);
            $count++;
        }

        return $count;
    }
}
