<?php

namespace Tests\Feature;

use App\Models\RfidDevice;
use App\Models\School;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RfidScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_missing_api_key()
    {
        $response = $this->postJson('/api/rfid/scan', [
            'card_uid' => 'ABCD1234',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'API Key tidak valid atau tidak ditemukan.']);
    }

    public function test_it_records_attendance_for_valid_scan()
    {
        $school = School::create([
            'name' => 'Test School',
            'slug' => 'test-school',
        ]);

        $device = RfidDevice::create([
            'school_id' => $school->id,
            'device_name' => 'Gate 1',
            'api_key' => 'valid-api-key-12345',
            'status' => 'active',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'nis' => '12345',
            'name' => 'John Doe',
            'rfid' => 'ABCD1234',
        ]);

        $response = $this->postJson('/api/rfid/scan', [
            'card_uid' => 'ABCD1234',
        ], [
            'X-Device-API-Key' => 'valid-api-key-12345',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'student_name' => 'John Doe',
                 ]);

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);
    }
}
