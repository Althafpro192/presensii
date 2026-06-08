<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Inertia middleware that shares common data with all Vue pages.
 */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $school = $request->attributes->get('school');

        // Build school features map
        $schoolFeatures = [];
        if ($school) {
            $features = $school->features()->pluck('is_enabled', 'feature_name');
            $schoolFeatures = $features->toArray();
        } elseif ($user && $user->school_id) {
            $features = \App\Models\SchoolFeature::where('school_id', $user->school_id)
                ->pluck('is_enabled', 'feature_name');
            $schoolFeatures = $features->toArray();
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->value,
                    'role_label' => $user->role?->label(),
                    'school_id' => $user->school_id,
                    'classroom_id' => $user->classroom_id,
                    'phone' => $user->phone,
                ] : null,
            ],
            'school' => $school ? [
                'id' => $school->id,
                'name' => $school->name,
                'slug' => $school->slug,
                'logo' => $school->logo,
                'primary_color' => $school->primary_color,
                'secondary_color' => $school->secondary_color,
            ] : null,
            'schoolFeatures' => $schoolFeatures,
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'warning' => fn() => $request->session()->get('warning'),
            ],
        ]);
    }
}
