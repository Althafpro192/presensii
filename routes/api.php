<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/rfid/scan', [App\Http\Controllers\Api\RfidScanController::class, 'scan'])
    ->middleware('verify.device.api.key');

Route::post('/rfid/write-callback', [App\Http\Controllers\Api\RfidWriteCallbackController::class, 'callback'])
    ->middleware('verify.device.api.key');

Route::prefix('whatsapp')->middleware('verify.webhook.secret')->group(function () {
    Route::post('/validate-parent', [App\Http\Controllers\Api\WhatsAppBotController::class, 'validateParent']);
    Route::post('/attendance', [App\Http\Controllers\Api\WhatsAppBotController::class, 'attendance']);
    Route::post('/homeroom', [App\Http\Controllers\Api\WhatsAppBotController::class, 'homeroom']);
    Route::post('/leave-request', [App\Http\Controllers\Api\WhatsAppBotController::class, 'leaveRequest']);
});