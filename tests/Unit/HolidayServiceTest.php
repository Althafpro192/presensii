<?php

namespace Tests\Unit;

use App\Models\GovernmentHoliday;
use App\Models\School;
use App\Models\SchoolHoliday;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayServiceTest extends TestCase
{
    use RefreshDatabase;

    protected HolidayService $service;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HolidayService();
        $this->school = School::create([
            'name' => 'Test School',
            'slug' => 'test-school',
        ]);
    }

    public function test_it_identifies_weekends_as_holidays()
    {
        $sunday = Carbon::parse('2024-01-07'); // Sunday
        $this->assertTrue($this->service->isHoliday($sunday, $this->school->id));
        
        $monday = Carbon::parse('2024-01-08'); // Monday
        $this->assertFalse($this->service->isHoliday($monday, $this->school->id));
    }

    public function test_it_prioritizes_school_override_masuk()
    {
        // Monday, but marked as libur nasional
        $monday = Carbon::parse('2024-01-08');
        GovernmentHoliday::create([
            'holiday_date' => $monday->toDateString(),
            'name' => 'Libur Nasional',
            'is_national' => true,
            'year' => 2024,
        ]);
        
        // Assert it's a holiday
        $this->assertTrue($this->service->isHoliday($monday, $this->school->id));

        // Create override "masuk"
        SchoolHoliday::create([
            'school_id' => $this->school->id,
            'event_date' => $monday->toDateString(),
            'type' => 'masuk',
            'override_government' => true,
        ]);

        // Assert it's NOT a holiday anymore
        $this->assertFalse($this->service->isHoliday($monday, $this->school->id));
    }
}
