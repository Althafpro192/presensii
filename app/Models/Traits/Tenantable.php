<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait Tenantable
 *
 * Automatically scopes queries by school_id from the current request context.
 * All models with a school_id column should use this trait.
 */
trait Tenantable
{
    /**
     * Boot the tenantable trait and add global scope.
     */
    public static function bootTenantable(): void
    {
        // Add global scope to filter by current school
        static::addGlobalScope('tenant', function (Builder $builder) {
            $schoolId = static::resolveCurrentSchoolId();
            if ($schoolId) {
                $builder->where($builder->getModel()->getTable() . '.school_id', $schoolId);
            }
        });

        // Automatically set school_id when creating a new record
        static::creating(function ($model) {
            if (empty($model->school_id)) {
                $model->school_id = static::resolveCurrentSchoolId();
            }
        });
    }

    /**
     * Resolve the current school ID from the request or session.
     */
    protected static function resolveCurrentSchoolId(): ?int
    {
        // Priority: request attribute > session > null
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return null; // Don't apply scope in console commands
        }

        $request = request();

        // Check request attribute (set by IdentifySchoolFromSubdomain middleware)
        if ($request && $request->attributes->get('school_id')) {
            return (int) $request->attributes->get('school_id');
        }

        // Check session
        if ($request && $request->session() && $request->session()->has('school_id')) {
            return (int) $request->session()->get('school_id');
        }

        // Check authenticated user's school_id
        if (auth()->check() && auth()->user()->school_id) {
            return (int) auth()->user()->school_id;
        }

        return null;
    }

    /**
     * Query without the tenant scope applied.
     */
    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope('tenant');
    }
}
