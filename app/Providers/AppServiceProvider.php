<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Student::observe(\App\Observers\AuditObserver::class);
        \App\Models\Attendance::observe(\App\Observers\AuditObserver::class);
        \App\Models\LeaveRequest::observe(\App\Observers\AuditObserver::class);
        \App\Models\RfidCardAssignment::observe(\App\Observers\AuditObserver::class);
        \App\Models\RfidDevice::observe(\App\Observers\AuditObserver::class);
        \App\Models\SchoolFeature::observe(\App\Observers\AuditObserver::class);
    }
}
