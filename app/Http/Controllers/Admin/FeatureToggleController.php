<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolFeature;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeatureToggleController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $features = SchoolFeature::where('school_id', $schoolId)->get();

        return Inertia::render('Admin/Features/Index', [
            'features' => $features,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'features' => 'required|array',
            'features.*.feature_name' => 'required|string',
            'features.*.is_enabled' => 'required|boolean',
        ]);

        $schoolId = $request->user()->school_id;

        foreach ($validated['features'] as $featureData) {
            SchoolFeature::where('school_id', $schoolId)
                ->where('feature_name', $featureData['feature_name'])
                ->update(['is_enabled' => $featureData['is_enabled']]);
        }

        return redirect()->back()->with('success', 'Fitur berhasil diperbarui.');
    }
}
