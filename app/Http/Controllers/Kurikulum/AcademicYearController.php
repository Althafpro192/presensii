<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::where('school_id', $request->user()->school_id)
            ->orderByDesc('start_date')
            ->get();

        return Inertia::render('Kurikulum/AcademicYears/Index', [
            'academicYears' => $academicYears,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $validated['school_id'] = $request->user()->school_id;

        if ($validated['is_current'] ?? false) {
            AcademicYear::where('school_id', $validated['school_id'])->update(['is_current' => false]);
        }

        AcademicYear::create($validated);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        if ($validated['is_current'] ?? false) {
            AcademicYear::where('school_id', $academicYear->school_id)->update(['is_current' => false]);
        }

        $academicYear->update($validated);

        return redirect()->back()->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->back()->with('success', 'Tahun Ajaran berhasil dihapus.');
    }
}
