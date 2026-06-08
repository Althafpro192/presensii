<?php

namespace App\Services;

use App\Enums\PromotionStatus;
use App\Models\AcademicCalendar;
use App\Models\Classroom;
use App\Models\PromotionLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service for student grade promotion logic.
 */
class PromotionService
{
    /**
     * Apply promotions for all students with a set promotion_status.
     *
     * @throws \Exception if not within kenaikan_kelas period
     */
    public function applyPromotions(int $schoolId, int $processedByUserId): array
    {
        // Verify we're within the kenaikan_kelas period
        $today = Carbon::today();
        $inPromotionPeriod = AcademicCalendar::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('type', 'kenaikan_kelas')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if (!$inPromotionPeriod) {
            throw new \Exception('Kenaikan kelas hanya bisa dilakukan dalam periode yang telah ditentukan di kalender akademik.');
        }

        $results = ['promoted' => 0, 'graduated' => 0, 'retained' => 0, 'errors' => []];

        DB::transaction(function () use ($schoolId, $processedByUserId, &$results) {
            $students = Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->whereIn('promotion_status', [
                    PromotionStatus::Naik->value,
                    PromotionStatus::TidakNaik->value,
                    PromotionStatus::Lulus->value,
                ])
                ->get();

            foreach ($students as $student) {
                $fromClassroomId = $student->classroom_id;

                match ($student->promotion_status) {
                    PromotionStatus::Naik => $this->promoteStudent($student, $schoolId, $processedByUserId, $fromClassroomId, $results),
                    PromotionStatus::Lulus => $this->graduateStudent($student, $schoolId, $processedByUserId, $fromClassroomId, $results),
                    PromotionStatus::TidakNaik => $this->retainStudent($student, $schoolId, $processedByUserId, $fromClassroomId, $results),
                    default => null,
                };
            }
        });

        return $results;
    }

    /**
     * Promote a student to the next grade level.
     */
    protected function promoteStudent(Student $student, int $schoolId, int $processedByUserId, ?int $fromClassroomId, array &$results): void
    {
        $currentClassroom = $student->classroom;

        if (!$currentClassroom) {
            $results['errors'][] = "Siswa {$student->name}: tidak memiliki kelas.";
            return;
        }

        // Find next classroom (grade_level + 1, smallest order)
        $nextClassroom = Classroom::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('grade_level', $currentClassroom->grade_level + 1)
            ->orderBy('order')
            ->first();

        if (!$nextClassroom) {
            // No higher grade available - mark as graduated instead
            $this->graduateStudent($student, $schoolId, $processedByUserId, $fromClassroomId, $results);
            return;
        }

        $student->update([
            'classroom_id' => $nextClassroom->id,
            'promotion_status' => PromotionStatus::Pending,
        ]);

        PromotionLog::create([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'from_classroom_id' => $fromClassroomId,
            'to_classroom_id' => $nextClassroom->id,
            'status' => 'naik',
            'processed_by' => $processedByUserId,
        ]);

        $results['promoted']++;
    }

    /**
     * Graduate a student.
     */
    protected function graduateStudent(Student $student, int $schoolId, int $processedByUserId, ?int $fromClassroomId, array &$results): void
    {
        $student->update([
            'classroom_id' => null,
            'graduated_at' => now(),
            'promotion_status' => PromotionStatus::Pending,
        ]);

        PromotionLog::create([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'from_classroom_id' => $fromClassroomId,
            'to_classroom_id' => null,
            'status' => 'lulus',
            'processed_by' => $processedByUserId,
        ]);

        $results['graduated']++;
    }

    /**
     * Retain a student in the same grade.
     */
    protected function retainStudent(Student $student, int $schoolId, int $processedByUserId, ?int $fromClassroomId, array &$results): void
    {
        $student->update([
            'promotion_status' => PromotionStatus::Pending,
        ]);

        PromotionLog::create([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'from_classroom_id' => $fromClassroomId,
            'to_classroom_id' => $fromClassroomId,
            'status' => 'tidak_naik',
            'processed_by' => $processedByUserId,
        ]);

        $results['retained']++;
    }
}
