<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'auth' => [
            'user' => auth()->user() ? [
                'name' => auth()->user()->name,
            ] : null,
        ],
    ]);
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    // Dashboard routing based on roles
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/super-admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/parent/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('parent.dashboard');

    // Super Admin Routes
    Route::resource('super-admin/schools', App\Http\Controllers\SuperAdmin\SchoolController::class)
        ->names('super-admin.schools');

    // Admin Sekolah Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
        Route::resource('classrooms', App\Http\Controllers\Admin\ClassroomController::class);
        Route::resource('rfid-cards', App\Http\Controllers\Admin\RfidCardController::class);
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        
        Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('features', [App\Http\Controllers\Admin\FeatureToggleController::class, 'index'])->name('features.index');
        Route::put('features', [App\Http\Controllers\Admin\FeatureToggleController::class, 'update'])->name('features.update');
        
        Route::get('bulk-write', [App\Http\Controllers\Admin\BulkWriteController::class, 'index'])->name('bulk-write.index');
        Route::post('bulk-write', [App\Http\Controllers\Admin\BulkWriteController::class, 'store'])->name('bulk-write.store');

        Route::resource('rfid-devices', App\Http\Controllers\Admin\RfidDeviceController::class);
    });

    // Kurikulum Routes
    Route::prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::resource('academic-years', App\Http\Controllers\Kurikulum\AcademicYearController::class);
        Route::resource('homeroom-assign', App\Http\Controllers\Kurikulum\HomeroomAssignController::class)->only(['index', 'store', 'destroy']);

        Route::get('calendar', [App\Http\Controllers\Kurikulum\CalendarController::class, 'index'])->name('calendar.index');
        Route::post('calendar/holiday', [App\Http\Controllers\Kurikulum\CalendarController::class, 'storeHoliday'])->name('calendar.holiday.store');
        Route::post('calendar/event', [App\Http\Controllers\Kurikulum\CalendarController::class, 'storeCalendarEvent'])->name('calendar.event.store');
        Route::delete('calendar/holiday/{holiday}', [App\Http\Controllers\Kurikulum\CalendarController::class, 'destroyHoliday'])->name('calendar.holiday.destroy');
        
        Route::get('promotion', [App\Http\Controllers\Kurikulum\PromotionController::class, 'index'])->name('promotion.index');
        Route::put('promotion/student/{student}', [App\Http\Controllers\Kurikulum\PromotionController::class, 'updateStatus'])->name('promotion.student.update');
        Route::post('promotion/bulk-update', [App\Http\Controllers\Kurikulum\PromotionController::class, 'bulkUpdate'])->name('promotion.bulk-update');
        Route::post('promotion/apply', [App\Http\Controllers\Kurikulum\PromotionController::class, 'apply'])->name('promotion.apply');
    });


    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('attendance', [App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
        Route::put('attendance/{attendance}', [App\Http\Controllers\Teacher\AttendanceController::class, 'update'])->name('attendance.update');
        
        Route::get('leave-requests', [App\Http\Controllers\Teacher\LeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::put('leave-requests/{leaveRequest}/approve', [App\Http\Controllers\Teacher\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::put('leave-requests/{leaveRequest}/reject', [App\Http\Controllers\Teacher\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

        Route::get('students', [App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');
        Route::post('students/{student}/report-lost-card', [App\Http\Controllers\Teacher\StudentController::class, 'reportLostCard'])->name('students.report-lost-card');
        Route::post('students/{student}/violations', [App\Http\Controllers\Teacher\StudentController::class, 'storeViolation'])->name('students.violations.store');
    });

    // Parent Routes
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('leave-requests/create', [App\Http\Controllers\ParentRole\LeaveRequestController::class, 'create'])->name('leave-requests.create');
        Route::post('leave-requests', [App\Http\Controllers\ParentRole\LeaveRequestController::class, 'store'])->name('leave-requests.store');
    });
});

require __DIR__.'/auth.php';