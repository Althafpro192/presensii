<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromView, ShouldAutoSize
{
    protected $classroomId;
    protected $date;

    public function __construct($classroomId, $date)
    {
        $this->classroomId = $classroomId;
        $this->date = Carbon::parse($date);
    }

    public function view(): View
    {
        $classroom = Classroom::findOrFail($this->classroomId);
        $school = $classroom->school;

        $students = Student::withoutGlobalScopes()
            ->where('classroom_id', $this->classroomId)
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $attendance = Attendance::withoutGlobalScopes()
                    ->where('student_id', $student->id)
                    ->whereDate('date', $this->date)
                    ->first();

                return [
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'status' => $attendance ? $attendance->status->label() : 'Belum Presensi',
                    'check_in' => $attendance ? $attendance->check_in_time : '-',
                    'check_out' => $attendance ? $attendance->check_out_time : '-',
                ];
            });

        return view('exports.attendance', [
            'students' => $students,
            'classroom' => $classroom,
            'school' => $school,
            'date' => $this->date->format('d/m/Y'),
        ]);
    }
}
