<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AttendanceArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendancesArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendances:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive attendances older than 1 year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneYearAgo = Carbon::now()->subYear()->toDateString();
        
        $this->info("Archiving attendances older than {$oneYearAgo}...");
        
        DB::transaction(function () use ($oneYearAgo) {
            $attendances = Attendance::where('date', '<', $oneYearAgo)->get();
            
            $count = 0;
            foreach ($attendances as $attendance) {
                AttendanceArchive::create(array_merge($attendance->toArray(), [
                    'archived_at' => Carbon::now()
                ]));
                
                $attendance->delete();
                $count++;
            }
            
            $this->info("Successfully archived {$count} attendance records.");
        });
    }
}
