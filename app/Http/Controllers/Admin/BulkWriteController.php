<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\RfidDevice;
use App\Models\RfidWriteJob;
use App\Models\Student;
use App\Jobs\ProcessRfidWriteJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BulkWriteController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('order')->get();
        $devices = RfidDevice::where('status', 'active')->get();

        $students = collect();
        if ($request->classroom_id) {
            $students = Student::where('classroom_id', $request->classroom_id)
                ->with(['cardAssignments' => fn($q) => $q->latest('assigned_at')])
                ->orderBy('name')
                ->get();
        }

        $pendingJobs = RfidWriteJob::whereIn('status', ['pending', 'processing'])
            ->with(['student', 'device'])
            ->latest()
            ->limit(20)
            ->get();

        return Inertia::render('Admin/BulkWrite/Index', [
            'classrooms' => $classrooms,
            'devices' => $devices,
            'students' => $students,
            'pendingJobs' => $pendingJobs,
            'filters' => $request->only('classroom_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|exists:rfid_devices,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $schoolId = $request->user()->school_id;

        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue;

            RfidWriteJob::create([
                'school_id' => $schoolId,
                'rfid_device_id' => $validated['device_id'],
                'student_id' => $studentId,
                'card_uid_to_write' => $student->nis,
                'status' => 'pending',
            ]);
        }

        // Dispatch queue jobs
        $pendingJobs = RfidWriteJob::where('school_id', $schoolId)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingJobs as $job) {
            ProcessRfidWriteJob::dispatch($job);
        }

        return redirect()->route('bulk-write.index')
            ->with('success', count($validated['student_ids']) . ' job penulisan kartu telah dijadwalkan.');
    }
}
