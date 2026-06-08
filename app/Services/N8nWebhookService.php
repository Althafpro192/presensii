<?php

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nWebhookService
{
    /**
     * Send attendance notification to N8N webhook.
     *
     * @param School $school The school model (should contain n8n webhook URL in settings)
     * @param Student $student The student who tapped
     * @param string $status e.g., 'Masuk', 'Terlambat', 'Pulang'
     * @param string $timestamp
     * @return bool True if successful, false otherwise.
     */
    public function sendAttendanceNotification(School $school, Student $student, string $status, string $timestamp): bool
    {
        // Get Webhook URL from school settings, fallback to a default or return false
        $webhookUrl = $school->settings['n8n_webhook_url'] ?? env('N8N_WEBHOOK_URL');

        if (!$webhookUrl) {
            Log::warning("N8n Webhook URL not configured for school ID: {$school->id}");
            return false;
        }

        // Only send if parent_phone is available
        if (!$student->parent_phone) {
            Log::info("N8n webhook skipped. Parent phone not available for student: {$student->nis}");
            return false;
        }

        $payload = [
            'school_name' => $school->name,
            'student_name' => $student->name,
            'student_nis' => $student->nis,
            'classroom' => $student->classroom ? $student->classroom->name : '-',
            'parent_phone' => $student->parent_phone,
            'status' => $status,
            'timestamp' => $timestamp,
        ];

        try {
            $response = Http::timeout(5)->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info("N8n webhook sent successfully for student {$student->nis}");
                return true;
            }

            Log::error("N8n webhook failed for student {$student->nis}. Status: {$response->status()}");
            return false;
        } catch (\Exception $e) {
            Log::error("N8n webhook connection error for student {$student->nis}: " . $e->getMessage());
            return false;
        }
    }
}
