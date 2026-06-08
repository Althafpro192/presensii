<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomTeacher;
use App\Models\GovernmentHoliday;
use App\Models\RfidCardAssignment;
use App\Models\RfidDevice;
use App\Models\School;
use App\Models\SchoolFeature;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create Super Admin ──────────────────────────────────
        User::firstOrCreate(
            ['email' => 'superadmin@presensi.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'role' => UserRole::SuperAdmin,
                'school_id' => null,
                'phone' => '081200000000',
            ]
        );

        // ── 2. Create Schools ──────────────────────────────────────
        $schools = [
            [
                'name' => 'SMA Negeri 1 Jakarta',
                'slug' => 'sman1-jakarta',
                'address' => 'Jl. Budi Utomo No. 7, Jakarta Pusat',
                'primary_color' => '#4361ee',
                'secondary_color' => '#3f37c9',
                'settings' => [
                    'late_threshold' => '07:00:00',
                    'absent_threshold' => '15:00:00',
                    'late_tolerance_minutes' => 15,
                    'weekend_days' => [0, 6], // Sunday, Saturday
                ],
            ],
            [
                'name' => 'SMA Negeri 2 Bandung',
                'slug' => 'sman2-bandung',
                'address' => 'Jl. Cihampelas No. 173, Bandung',
                'primary_color' => '#7c3aed',
                'secondary_color' => '#6d28d9',
                'settings' => [
                    'late_threshold' => '06:45:00',
                    'absent_threshold' => '14:30:00',
                    'late_tolerance_minutes' => 10,
                    'weekend_days' => [0, 6],
                ],
            ],
        ];

        foreach ($schools as $schoolData) {
            $school = School::firstOrCreate(
                ['slug' => $schoolData['slug']],
                $schoolData
            );

            // Create features for each school
            foreach (SchoolFeature::FEATURES as $feature) {
                SchoolFeature::firstOrCreate(
                    ['school_id' => $school->id, 'feature_name' => $feature],
                    ['is_enabled' => true]
                );
            }

            // Create academic year
            $academicYear = AcademicYear::firstOrCreate(
                ['school_id' => $school->id, 'name' => '2025/2026'],
                [
                    'start_date' => '2025-07-14',
                    'end_date' => '2026-06-30',
                    'is_current' => true,
                ]
            );

            // Create classrooms (3 grade levels, 2 classes each)
            $classrooms = [];
            foreach ([10, 11, 12] as $grade) {
                foreach (['IPA 1', 'IPS 1'] as $i => $suffix) {
                    $classrooms[] = Classroom::firstOrCreate(
                        ['school_id' => $school->id, 'name' => "$grade $suffix"],
                        [
                            'grade_level' => $grade,
                            'order' => $i,
                        ]
                    );
                }
            }

            // Create admin sekolah
            User::firstOrCreate(
                ['email' => "admin@{$school->slug}.com"],
                [
                    'name' => "Admin {$school->name}",
                    'password' => 'password',
                    'role' => UserRole::AdminSekolah,
                    'school_id' => $school->id,
                    'phone' => '0812' . rand(10000000, 99999999),
                ]
            );

            // Create kurikulum
            User::firstOrCreate(
                ['email' => "kurikulum@{$school->slug}.com"],
                [
                    'name' => "Kurikulum {$school->name}",
                    'password' => 'password',
                    'role' => UserRole::Kurikulum,
                    'school_id' => $school->id,
                    'phone' => '0813' . rand(10000000, 99999999),
                ]
            );

            // Create teachers (one per classroom as homeroom)
            foreach ($classrooms as $classroom) {
                $teacher = User::firstOrCreate(
                    ['email' => "guru.{$classroom->id}@{$school->slug}.com"],
                    [
                        'name' => "Guru Wali Kelas {$classroom->name}",
                        'password' => 'password',
                        'role' => UserRole::Teacher,
                        'school_id' => $school->id,
                        'classroom_id' => $classroom->id,
                        'phone' => '0814' . rand(10000000, 99999999),
                    ]
                );

                // Assign as homeroom teacher
                ClassroomTeacher::firstOrCreate([
                    'academic_year_id' => $academicYear->id,
                    'classroom_id' => $classroom->id,
                    'teacher_id' => $teacher->id,
                ]);
            }

            // Create RFID device
            $device = RfidDevice::firstOrCreate(
                ['school_id' => $school->id, 'device_name' => 'ESP32 Gerbang Utama'],
                ['ip_address' => '192.168.1.100']
            );

            // Create students (5 per classroom) with RFID cards
            foreach ($classrooms as $classroom) {
                for ($s = 1; $s <= 5; $s++) {
                    $nis = $school->id . str_pad($classroom->id, 3, '0', STR_PAD_LEFT) . str_pad($s, 3, '0', STR_PAD_LEFT);
                    $rfidUid = strtoupper(Str::random(8));

                    $student = Student::firstOrCreate(
                        ['nis' => $nis],
                        [
                            'school_id' => $school->id,
                            'classroom_id' => $classroom->id,
                            'name' => "Siswa {$classroom->name} #{$s}",
                            'rfid' => $rfidUid,
                            'parent_phone' => '0815' . rand(10000000, 99999999),
                        ]
                    );

                    // Create RFID card assignment (only if student was just created)
                    if ($student->wasRecentlyCreated) {
                        RfidCardAssignment::firstOrCreate(
                            ['card_uid' => $student->rfid],
                            [
                                'student_id' => $student->id,
                                'rfid_device_id' => $device->id,
                                'status' => 'active',
                                'assigned_at' => now(),
                            ]
                        );
                    }

                    // Create parent for first 2 students per class
                    if ($s <= 2) {
                        $parent = User::firstOrCreate(
                            ['email' => "ortu.{$student->id}@{$school->slug}.com"],
                            [
                                'name' => "Orang Tua {$student->name}",
                                'password' => 'password',
                                'role' => UserRole::OrangTua,
                                'school_id' => $school->id,
                                'phone' => $student->parent_phone,
                            ]
                        );

                        // Link parent to student
                        if (!$parent->students()->where('student_id', $student->id)->exists()) {
                            $parent->students()->attach($student->id);
                        }
                    }
                }
            }
        }

        // ── 3. Seed Government Holidays ────────────────────────────
        $holidays = [
            ['holiday_date' => '2026-01-01', 'name' => 'Tahun Baru Masehi', 'year' => 2026],
            ['holiday_date' => '2026-03-22', 'name' => 'Isra Miraj', 'year' => 2026],
            ['holiday_date' => '2026-03-29', 'name' => 'Hari Raya Nyepi', 'year' => 2026],
            ['holiday_date' => '2026-04-03', 'name' => 'Wafat Isa Almasih', 'year' => 2026],
            ['holiday_date' => '2026-05-01', 'name' => 'Hari Buruh', 'year' => 2026],
            ['holiday_date' => '2026-05-17', 'name' => 'Kenaikan Isa Almasih', 'year' => 2026],
            ['holiday_date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila', 'year' => 2026],
            ['holiday_date' => '2026-08-17', 'name' => 'Hari Kemerdekaan RI', 'year' => 2026],
            ['holiday_date' => '2026-12-25', 'name' => 'Hari Natal', 'year' => 2026],
        ];

        foreach ($holidays as $holiday) {
            GovernmentHoliday::firstOrCreate(
                ['holiday_date' => $holiday['holiday_date']],
                array_merge($holiday, ['is_national' => true])
            );
        }
    }
}
