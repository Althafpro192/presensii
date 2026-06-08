<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\GovernmentHoliday;
use Carbon\Carbon;

class HolidaysSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holidays:sync {--year= : The year to sync holidays for (defaults to current year)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync government holidays from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year') ?: Carbon::now()->year;
        
        $this->info("Fetching holidays for year {$year}...");
        
        try {
            $response = Http::get("https://api-harilibur.vercel.app/api?year={$year}");
            
            if ($response->successful()) {
                $holidays = $response->json();
                $count = 0;
                
                foreach ($holidays as $holiday) {
                    if (isset($holiday['is_national_holiday']) && $holiday['is_national_holiday']) {
                        GovernmentHoliday::updateOrCreate(
                            ['holiday_date' => $holiday['holiday_date']],
                            [
                                'name' => $holiday['holiday_name'],
                                'is_national' => true,
                                'year' => $year,
                            ]
                        );
                        $count++;
                    }
                }
                
                $this->info("Successfully synced {$count} national holidays for {$year}.");
            } else {
                $this->error("Failed to fetch holidays. API returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
        }
    }
}
