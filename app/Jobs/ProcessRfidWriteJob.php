<?php

namespace App\Jobs;

use App\Models\RfidWriteJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessRfidWriteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Retry 3 times
    public $backoff = 5; // Wait 5 seconds before retrying

    protected $rfidWriteJob;

    /**
     * Create a new job instance.
     */
    public function __construct(RfidWriteJob $rfidWriteJob)
    {
        $this->rfidWriteJob = $rfidWriteJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load relationships
        $this->rfidWriteJob->load(['device', 'student']);
        
        $device = $this->rfidWriteJob->device;
        
        if (!$device || !$device->ip_address) {
            $this->markAsFailed('Device IP address not configured.');
            return;
        }

        $this->rfidWriteJob->update(['status' => 'processing']);

        try {
            // POST request to the ESP32 local IP to initiate the write
            // The ESP32 is expected to expose a /write_rfid endpoint
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Device-API-Key' => $device->api_key
                ])
                ->post("http://{$device->ip_address}/write_rfid", [
                    'uid_to_write' => clone $this->rfidWriteJob->card_uid_to_write,
                    'job_id' => $this->rfidWriteJob->id,
                ]);

            if ($response->successful()) {
                // The ESP32 accepted the request. It might process it asynchronously.
                // It should call back the server via `/api/rfid/write-callback` to update the final status.
                // For now, we will keep the status as processing or set it to 'sent_to_device'
                Log::info("RfidWriteJob {$this->rfidWriteJob->id} sent to device {$device->ip_address}.");
            } else {
                $this->markAsFailed("ESP32 responded with status code: {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to connect to ESP32 for Job {$this->rfidWriteJob->id}: " . $e->getMessage());
            
            // If it's a connection error, releasing back to queue for retry
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            } else {
                $this->markAsFailed('Connection to ESP32 failed after ' . $this->tries . ' attempts. Error: ' . $e->getMessage());
            }
        }
    }

    protected function markAsFailed(string $errorMsg)
    {
        $this->rfidWriteJob->update([
            'status' => 'failed',
            'error_message' => $errorMsg,
            'processed_at' => now(),
        ]);
        Log::error("RfidWriteJob {$this->rfidWriteJob->id} failed: {$errorMsg}");
    }
}
