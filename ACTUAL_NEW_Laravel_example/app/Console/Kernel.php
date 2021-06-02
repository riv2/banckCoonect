<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
//use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Gecche\Multidomain\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('gocpa:postbacks')->everyMinute();
        $schedule->command('gocpa:onboardingMailing')->everyMinute();
        $schedule->command('gocpa:downloadxmlfeeds')->dailyAt('07:00');
        $schedule->command('checker:GrossAmountInOrders')->hourly()->unlessBetween('21:00', '9:00');
        if (config('app.gocpa_project') == 'cloud') {
            $schedule->command('gocpa:checkapi hsr')->everyMinute();
            $schedule->command('checker:pixelLog')->hourly()->unlessBetween('21:00', '9:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
