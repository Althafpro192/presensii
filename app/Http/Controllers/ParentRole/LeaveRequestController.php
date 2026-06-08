<?php

namespace App\Http\Controllers\ParentRole;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function __construct(
        protected ImageCompressionService $imageService,
    ) {}

    public function create(Request $request)
    {
        $children = $request->user()->students()->with('classroom')->get();

        return Inertia::render('Parent/LeaveRequest/Create', [
            'children' => $children,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'type' => 'required|in:izin,sakit',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max upload
        ]);

        // Verify parent has access to this student
        $parent = $request->user();
        $hasAccess = $parent->students()->where('students.id', $validated['student_id'])->exists();

        if (!$hasAccess) {
            return redirect()->back()->withErrors(['student_id' => 'Anda tidak memiliki akses ke siswa ini.']);
        }

        $student = \App\Models\Student::find($validated['student_id']);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->imageService->processAttachment($request->file('attachment'));
        }

        LeaveRequest::create([
            'school_id' => $student->school_id,
            'student_id' => $validated['student_id'],
            'parent_id' => $parent->id,
            'date' => $validated['date'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Surat izin berhasil dikirim.');
    }
}
