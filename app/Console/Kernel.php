<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
	    $schedule->command('app:moderate-texts')->hourly();
	    $schedule->command('articles:update-rankings')
		    ->daily()
		    ->at('09:00') // Run at 9 AM
		    ->withoutOverlapping()
		    ->runInBackground();

	    $schedule->command('articles:update-stats')
		    ->daily()
		    ->at('03:00') // Run at 3 AM
		    ->withoutOverlapping()
		    ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
