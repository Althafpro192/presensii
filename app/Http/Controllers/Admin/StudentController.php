<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Classroom;
use App\Models\Student;
use App\UseCases\Admin\Student\CreateStudentUseCase;
use App\UseCases\Admin\Student\UpdateStudentUseCase;
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

    public function store(StoreStudentRequest $request, CreateStudentUseCase $useCase)
    {
        $useCase->execute($request->validated(), $request->user());

        return redirect()->route('admin.students.index')
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

    public function update(UpdateStudentRequest $request, Student $student, UpdateStudentUseCase $useCase)
    {
        $useCase->execute($student, $request->validated());

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
