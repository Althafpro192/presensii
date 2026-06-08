<?php

namespace App\Http\Middleware;

use App\Models\SchoolFeature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if a feature is enabled for the current school.
 *
 * Usage: ->middleware('feature:pelanggaran_siswa')
 */
class FeatureEnabled
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $schoolId = $request->attributes->get('school_id')
            ?? $request->user()?->school_id;

        if (!$schoolId) {
            abort(403, 'Sekolah tidak teridentifikasi.');
        }

        $isEnabled = SchoolFeature::where('school_id', $schoolId)
            ->where('feature_name', $feature)
            ->where('is_enabled', true)
            ->exists();

        if (!$isEnabled) {
            abort(403, "Fitur '{$feature}' tidak aktif untuk sekolah ini.");
        }

        return $next($request);
    }
}
