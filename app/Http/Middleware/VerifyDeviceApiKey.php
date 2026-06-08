<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RfidDevice;
use Illuminate\Http\Request;

class VerifyDeviceApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Device-API-Key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key missing'], 401);
        }

        $device = RfidDevice::where('api_key', $apiKey)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Invalid or inactive API Key'], 401);
        }

        // Update last activity
        $device->update(['last_activity_at' => now()]);

        // Simpan device dan school_id ke request
        $request->merge([
            'device' => $device,
            'school_id' => $device->school_id,
        ]);

        return $next($request);
    }
}