<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%"))
            ->when($request->classroom_id, fn($q, $id) => $q->where('classroom_id', $id))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $classrooms = Classroom::orderBy('grade_level')->orderBy('order')->get();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'classrooms' => $classrooms,
            'filters' => $request->only(['search', 'classroom_id']),
        ]);
    }

    public function create()
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('order')->get();

        return Inertia::render('Admin/Students/Create', [
            'classrooms' => $classrooms,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students',
            'name' => 'required|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'rfid' => 'nullable|string|unique:students',
            'parent_phone' => 'nullable|string',
        ]);

        $validated['school_id'] = $request->user()->school_id;

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('order')->get();

        return Inertia::render('Admin/Students/Edit', [
            'student' => $student,
            'classrooms' => $classrooms,
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'rfid' => 'nullable|string|unique:students,rfid,' . $student->id,
            'parent_phone' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
