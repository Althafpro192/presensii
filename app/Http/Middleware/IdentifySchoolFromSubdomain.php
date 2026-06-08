<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to identify the current school from the subdomain.
 *
 * - If subdomain is 'admin' or empty → super admin access (no school binding)
 * - Otherwise → find school by slug, bind to request and session
 */
class IdentifySchoolFromSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $appDomain = config('app.domain', 'localhost');

        // Extract subdomain
        $subdomain = str_replace('.' . $appDomain, '', $host);

        // If same as domain (no subdomain) or 'admin' → super admin area
        if ($subdomain === $appDomain || $subdomain === 'admin' || empty($subdomain)) {
            $request->attributes->set('school_id', null);
            $request->attributes->set('is_super_admin_area', true);
            return $next($request);
        }

        // Find school by slug
        $school = School::where('slug', $subdomain)->first();

        if (!$school) {
            abort(404, 'Sekolah tidak ditemukan.');
        }

        if (!$school->is_active) {
            abort(403, 'Sekolah tidak aktif. Hubungi administrator.');
        }

        // Bind school to request
        $request->attributes->set('school_id', $school->id);
        $request->attributes->set('school', $school);
        $request->attributes->set('is_super_admin_area', false);

        // Store in session for Tenantable trait
        if ($request->hasSession()) {
            $request->session()->put('school_id', $school->id);
        }

        return $next($request);
    }
}
