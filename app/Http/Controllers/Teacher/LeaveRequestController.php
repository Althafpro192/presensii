<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Enums\LeaveRequestStatus;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $classroom = $user->getHomeroomClassroom();

        $leaveRequests = LeaveRequest::with(['student', 'parent'])
            ->when($classroom, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('classroom_id', $classroom->id)))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Teacher/LeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            'classroom' => $classroom,
            'filters' => $request->only('status'),
        ]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => LeaveRequestStatus::Approved,
            'approved_by' => $request->user()->id,
        ]);

        // Update attendance for the leave date
        $student = $leaveRequest->student;
        $attendance = $student->attendances()
            ->where('date', $leaveRequest->date->toDateString())
            ->first();

        $status = $leaveRequest->type->value; // 'izin' or 'sakit'

        if ($attendance) {
            $attendance->update(['status' => $status]);
        } else {
            \App\Models\Attendance::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'date' => $leaveRequest->date,
                'status' => $status,
            ]);
        }

        return redirect()->back()->with('success', 'Surat izin disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => LeaveRequestStatus::Rejected,
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Surat izin ditolak.');
    }
}
