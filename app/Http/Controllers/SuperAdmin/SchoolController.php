<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount(['students', 'users', 'classrooms'])
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('SuperAdmin/Schools/Index', [
            'schools' => $schools,
        ]);
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Schools/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:schools',
            'address' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $validated['api_key'] = Str::random(32);
        $validated['settings'] = [
            'late_threshold' => '07:00:00',
            'absent_threshold' => '15:00:00',
            'late_tolerance_minutes' => 15,
            'weekend_days' => [0, 6],
        ];

        $school = School::create($validated);

        // Create default features
        foreach (SchoolFeature::FEATURES as $feature) {
            SchoolFeature::create([
                'school_id' => $school->id,
                'feature_name' => $feature,
                'is_enabled' => false,
            ]);
        }

        return redirect()->route('super-admin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function edit(School $school)
    {
        return Inertia::render('SuperAdmin/Schools/Edit', [
            'school' => $school,
        ]);
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:schools,slug,' . $school->id,
            'address' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $school->update($validated);

        return redirect()->route('super-admin.schools.index')
            ->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('super-admin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }
}
