<?php

use App\Http\Controllers\RfidDeviceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Manajemen Perangkat RFID
    Route::resource('rfid-devices', RfidDeviceController::class);
});

require __DIR__.'/auth.php';