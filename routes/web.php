<?php

use App\Http\Controllers\RfidDeviceController;

Route::middleware(['auth'])->group(function () {
    Route::resource('rfid-devices', RfidDeviceController::class);
});