<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

/**
 * API controller for ESP32 RFID scan endpoint.
 */
class RfidScanController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
    ) {}

    /**
     * POST /api/rfid/scan
     * Header: X-Device-API-Key
     * Body: { "card_uid": "ABCD1234" }
     */
    public function scan(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
        ]);

        $schoolId = $request->attributes->get('school_id');
        $cardUid = $request->input('card_uid');

        $result = $this->attendanceService->recordScan($cardUid, $schoolId);

        $statusCode = $result['success'] ? 200 : 422;

        return response()->json([
            'status' => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
            'student_name' => $result['student_name'] ?? null,
            'type' => $result['type'] ?? null,
        ], $statusCode);
    }
}
