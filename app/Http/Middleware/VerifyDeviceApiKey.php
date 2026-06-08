<?php

namespace App\Http\Middleware;

use App\Models\RfidDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to verify ESP32 device API key from X-Device-API-Key header.
 */
class VerifyDeviceApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Device-API-Key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key is required.',
            ], 401);
        }

        $device = RfidDevice::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive device API key.',
            ], 401);
        }

        // Update last activity timestamp
        $device->update(['last_activity_at' => now()]);

        // Bind device and school to request
        $request->attributes->set('rfid_device', $device);
        $request->attributes->set('school_id', $device->school_id);

        return $next($request);
    }
}