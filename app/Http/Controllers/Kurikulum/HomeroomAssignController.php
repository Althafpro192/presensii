<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeroomAssignController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderByDesc('start_date')->get();
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('grade_level')->orderBy('order')->get();
        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->get();
        
        $selectedYearId = $request->input('academic_year_id', $academicYears->firstWhere('is_current', true)?->id);
        
        $assignments = [];
        if ($selectedYearId) {
            $assignments = ClassroomTeacher::with(['classroom', 'teacher'])
                ->where('academic_year_id', $selectedYearId)
                ->get()
                ->keyBy('classroom_id');
        }

        return Inertia::render('Kurikulum/HomeroomAssign/Index', [
            'academicYears' => $academicYears,
            'classrooms' => $classrooms,
            'teachers' => $teachers,
            'assignments' => $assignments,
            'filters' => ['academic_year_id' => $selectedYearId],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        // Ensure user is actually a teacher in this school
        $teacher = User::where('id', $validated['teacher_id'])->where('role', 'teacher')->firstOrFail();

        ClassroomTeacher::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'classroom_id' => $validated['classroom_id'],
            ],
            [
                'teacher_id' => $teacher->id,
            ]
        );

        return redirect()->back()->with('success', 'Wali kelas berhasil ditetapkan.');
    }

    public function destroy(Request $request, ClassroomTeacher $homeroomAssign)
    {
        $homeroomAssign->delete();
        return redirect()->back()->with('success', 'Penugasan wali kelas dihapus.');
    }
}
