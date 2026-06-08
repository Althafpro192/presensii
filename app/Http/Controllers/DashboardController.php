<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\LeaveRequest;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::SuperAdmin => $this->superAdminDashboard(),
            UserRole::AdminSekolah => $this->adminDashboard($user),
            UserRole::Kurikulum => $this->kuriculumDashboard($user),
            UserRole::Teacher => $this->teacherDashboard($user),
            UserRole::OrangTua => $this->parentDashboard($user),
        };
    }

    protected function superAdminDashboard()
    {
        $schools = School::withCount(['students', 'users'])->get();

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => [
                'total_schools' => School::count(),
                'total_students' => Student::withoutGlobalScopes()->count(),
                'total_teachers' => User::where('role', UserRole::Teacher)->count(),
                'active_schools' => School::where('is_active', true)->count(),
            ],
            'schools' => $schools,
        ]);
    }

    protected function adminDashboard($user)
    {
        $schoolId = $user->school_id;
        $today = Carbon::today();

        $todayAttendance = Attendance::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('date', $today)
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthData = Attendance::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->selectRaw("status, COUNT(*) as count")
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $monthlyTrend[] = [
                'month' => $month->translatedFormat('M Y'),
                'data' => $monthData,
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_students' => Student::withoutGlobalScopes()->where('school_id', $schoolId)->count(),
                'total_classrooms' => Classroom::withoutGlobalScopes()->where('school_id', $schoolId)->count(),
                'total_teachers' => User::where('school_id', $schoolId)->where('role', UserRole::Teacher)->count(),
                'pending_leaves' => LeaveRequest::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', 'pending')->count(),
            ],
            'todayAttendance' => $todayAttendance,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }

    protected function kuriculumDashboard($user)
    {
        return $this->adminDashboard($user);
    }

    protected function teacherDashboard($user)
    {
        $classroom = $user->getHomeroomClassroom();
        $schoolId = $user->school_id;
        $today = Carbon::today();

        $classroomStudents = [];
        $todayAttendance = [];
        $pendingLeaves = 0;

        if ($classroom) {
            $classroomStudents = Student::withoutGlobalScopes()
                ->where('classroom_id', $classroom->id)
                ->count();

            $todayAttendance = Attendance::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('date', $today)
                ->whereHas('student', fn($q) => $q->where('classroom_id', $classroom->id))
                ->selectRaw("status, COUNT(*) as count")
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $pendingLeaves = LeaveRequest::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', 'pending')
                ->whereHas('student', fn($q) => $q->where('classroom_id', $classroom->id))
                ->count();
        }

        return Inertia::render('Teacher/Dashboard', [
            'classroom' => $classroom,
            'stats' => [
                'total_students' => $classroomStudents,
                'pending_leaves' => $pendingLeaves,
            ],
            'todayAttendance' => $todayAttendance,
        ]);
    }

    protected function parentDashboard($user)
    {
        $children = $user->students()->with('classroom')->get();
        $childrenData = [];

        foreach ($children as $child) {
            // Last 30 days attendance
            $attendance = Attendance::withoutGlobalScopes()
                ->where('student_id', $child->id)
                ->where('date', '>=', Carbon::now()->subDays(30))
                ->orderBy('date', 'desc')
                ->get();

            // Monthly summary (last 6 months)
            $monthlySummary = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $summary = Attendance::withoutGlobalScopes()
                    ->where('student_id', $child->id)
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->selectRaw("status, COUNT(*) as count")
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();

                $monthlySummary[] = [
                    'month' => $month->translatedFormat('M'),
                    'data' => $summary,
                ];
            }

            $childrenData[] = [
                'student' => $child,
                'attendance' => $attendance,
                'monthlySummary' => $monthlySummary,
            ];
        }

        return Inertia::render('Parent/Dashboard', [
            'children' => $childrenData,
        ]);
    }
}
