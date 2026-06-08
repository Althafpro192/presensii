<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\CardStatus;
use App\Enums\RfidWriteStatus;
use App\Models\RfidCardAssignment;
use App\Models\RfidWriteJob;
use Illuminate\Http\Request;

/**
 * API controller for ESP32 RFID write callback.
 */
class RfidWriteCallbackController extends Controller
{
    /**
     * POST /api/rfid/write-callback
     * Called by ESP32 after writing a card.
     * Body: { "job_id": 1, "status": "success", "card_uid": "NEW_UID" }
     */
    public function callback(Request $request)
    {
        $request->validate([
            'job_id' => 'required|integer',
            'status' => 'required|in:success,failed',
            'card_uid' => 'required_if:status,success|string|nullable',
            'error_message' => 'nullable|string',
        ]);

        $job = RfidWriteJob::withoutGlobalScopes()->find($request->input('job_id'));

        if (!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job not found.',
            ], 404);
        }

        if ($request->input('status') === 'success') {
            $cardUid = $request->input('card_uid');

            // Deactivate old cards for this student
            RfidCardAssignment::where('student_id', $job->student_id)
                ->where('status', CardStatus::Active)
                ->update(['status' => CardStatus::Inactive]);

            // Create new card assignment
            RfidCardAssignment::create([
                'student_id' => $job->student_id,
                'rfid_device_id' => $job->rfid_device_id,
                'card_uid' => $cardUid,
                'status' => CardStatus::Active,
                'assigned_at' => now(),
            ]);

            // Update student's RFID
            $job->student()->update(['rfid' => $cardUid]);

            $job->update([
                'status' => RfidWriteStatus::Success,
                'processed_at' => now(),
            ]);
        } else {
            $job->update([
                'status' => RfidWriteStatus::Failed,
                'error_message' => $request->input('error_message', 'Unknown error'),
                'processed_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Callback received.',
        ]);
    }
}
