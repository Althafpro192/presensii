<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentViolation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $classroom = $user->getHomeroomClassroom();

        if (!$classroom) {
            return Inertia::render('Teacher/Students/Index', [
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'students' => [],
            ]);
        }

        $students = Student::with(['violations' => function($q) {
                $q->latest()->take(5);
            }])
            ->where('classroom_id', $classroom->id)
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $student->has_lost_card = $student->hasLostCard();
                return $student;
            });

        return Inertia::render('Teacher/Students/Index', [
            'classroom' => $classroom,
            'students' => $students,
        ]);
    }

    public function reportLostCard(Request $request, Student $student)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        $activeCard = $student->activeCard();
        if ($activeCard) {
            $activeCard->markAsLost($request->note ?? 'Dilaporkan oleh wali kelas.');
        }

        return redirect()->back()->with('success', 'Kartu siswa berhasil dilaporkan hilang.');
    }

    public function storeViolation(Request $request, Student $student)
    {
        $validated = $request->validate([
            'violation_type' => 'required|string|max:255',
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $validated['school_id'] = $student->school_id;
        $validated['student_id'] = $student->id;
        $validated['reported_by'] = $request->user()->id;

        StudentViolation::create($validated);

        return redirect()->back()->with('success', 'Pelanggaran siswa berhasil dicatat.');
    }
}
