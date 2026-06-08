<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::where('school_id', $request->user()->school_id)
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('order')
            ->get();

        return Inertia::render('Admin/Classrooms/Index', [
            'classrooms' => $classrooms,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|in:10,11,12',
            'order' => 'integer',
        ]);

        $validated['school_id'] = $request->user()->school_id;
        
        Classroom::create($validated);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|in:10,11,12',
            'order' => 'integer',
        ]);

        $classroom->update($validated);

        return redirect()->back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        if ($classroom->students()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus kelas yang memiliki siswa.');
        }
        
        $classroom->delete();
        return redirect()->back()->with('success', 'Kelas berhasil dihapus.');
    }
}
