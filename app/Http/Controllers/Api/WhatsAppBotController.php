<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\LeaveType;
use App\Models\Attendance;
use App\Models\ClassroomTeacher;
use App\Models\AcademicYear;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * API controller for n8n WhatsApp bot integration.
 */
class WhatsAppBotController extends Controller
{
    /**
     * POST /api/whatsapp/validate-parent
     * Validate parent phone number and return their children.
     */
    public function validateParent(Request $request)
    {
        $request->validate(['phone_number' => 'required|string']);

        $phone = $this->normalizePhone($request->input('phone_number'));
        $schoolId = $request->attributes->get('school_id');

        $parent = User::where('phone', $phone)
            ->where('role', 'orang_tua')
            ->where('school_id', $schoolId)
            ->first();

        if (!$parent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor tidak terdaftar. Silakan laporkan ke wali kelas untuk mendaftarkan nomor Anda.',
            ]);
        }

        $children = $parent->students()->with('classroom')->get()->map(fn($s) => [
            'nis' => $s->nis,
            'name' => $s->name,
            'classroom' => $s->classroom?->name,
        ]);

        return response()->json([
            'status' => 'success',
            'parent_name' => $parent->name,
            'children' => $children,
        ]);
    }

    /**
     * POST /api/whatsapp/attendance
     * Get today's attendance for a student.
     */
    public function attendance(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'student_nis' => 'required|string',
        ]);

        $parent = $this->findParent($request);
        if (!$parent) {
            return $this->unauthorizedResponse();
        }

        $student = $this->findStudentForParent($parent, $request->input('student_nis'));
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan atau Anda tidak memiliki akses.',
            ]);
        }

        $today = Carbon::today();
        $attendance = Attendance::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('date', $today->toDateString())
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'success',
                'message' => "{$student->name} belum melakukan presensi hari ini.",
            ]);
        }

        return response()->json([
            'status' => 'success',
            'student_name' => $student->name,
            'date' => $attendance->date->format('d/m/Y'),
            'check_in' => $attendance->check_in_time,
            'check_out' => $attendance->check_out_time,
            'attendance_status' => $attendance->status->label(),
        ]);
    }

    /**
     * POST /api/whatsapp/homeroom
     * Get homeroom teacher info for a student.
     */
    public function homeroom(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'student_nis' => 'required|string',
        ]);

        $parent = $this->findParent($request);
        if (!$parent) {
            return $this->unauthorizedResponse();
        }

        $student = $this->findStudentForParent($parent, $request->input('student_nis'));
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan.',
            ]);
        }

        if (!$student->classroom_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa belum terdaftar di kelas.',
            ]);
        }

        $currentYear = AcademicYear::withoutGlobalScopes()
            ->where('school_id', $student->school_id)
            ->where('is_current', true)
            ->first();

        $assignment = null;
        if ($currentYear) {
            $assignment = ClassroomTeacher::where('academic_year_id', $currentYear->id)
                ->where('classroom_id', $student->classroom_id)
                ->with('teacher')
                ->first();
        }

        return response()->json([
            'status' => 'success',
            'student_name' => $student->name,
            'classroom' => $student->classroom?->name,
            'homeroom_teacher' => $assignment?->teacher?->name ?? 'Belum ditentukan',
            'homeroom_phone' => $assignment?->teacher?->phone,
        ]);
    }

    /**
     * POST /api/whatsapp/leave-request
     * Submit a leave request via WhatsApp.
     */
    public function leaveRequest(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'student_nis' => 'required|string',
            'type' => 'required|in:izin,sakit',
            'reason' => 'required|string|max:500',
            'date' => 'nullable|date',
        ]);

        $parent = $this->findParent($request);
        if (!$parent) {
            return $this->unauthorizedResponse();
        }

        $student = $this->findStudentForParent($parent, $request->input('student_nis'));
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan.',
            ]);
        }

        $date = $request->input('date', Carbon::today()->toDateString());

        $leave = LeaveRequest::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'date' => $date,
            'type' => LeaveType::from($request->input('type')),
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Pengajuan {$leave->type->label()} untuk {$student->name} berhasil dikirim. Menunggu persetujuan wali kelas.",
        ]);
    }

    // ── Private Helpers ────────────────────────────────────────────

    protected function findParent(Request $request): ?User
    {
        $phone = $this->normalizePhone($request->input('phone_number'));
        $schoolId = $request->attributes->get('school_id');

        return User::where('phone', $phone)
            ->where('role', 'orang_tua')
            ->where('school_id', $schoolId)
            ->first();
    }

    protected function findStudentForParent(User $parent, string $nis): ?Student
    {
        return $parent->students()->where('nis', $nis)->first();
    }

    protected function unauthorizedResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Nomor tidak terdaftar. Silakan laporkan ke wali kelas untuk mendaftarkan nomor Anda.',
        ]);
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove non-numeric characters except leading +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Convert +62 to 0
        if (str_starts_with($phone, '+62')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }
}
