<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $school = School::find($request->user()->school_id);

        return Inertia::render('Admin/Settings/Index', [
            'school' => $school,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'late_threshold' => 'required|date_format:H:i:s',
            'absent_threshold' => 'required|date_format:H:i:s',
            'late_tolerance_minutes' => 'required|integer|min:0|max:60',
        ]);

        $school = School::find($request->user()->school_id);

        $settings = $school->settings ?? [];
        $settings['late_threshold'] = $validated['late_threshold'];
        $settings['absent_threshold'] = $validated['absent_threshold'];
        $settings['late_tolerance_minutes'] = $validated['late_tolerance_minutes'];

        $school->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'settings' => $settings,
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
