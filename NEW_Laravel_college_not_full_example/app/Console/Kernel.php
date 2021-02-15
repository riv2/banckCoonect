<?php

namespace App\Console;

use App\Console\Commands\AdminSet;
use App\Console\Commands\AdminUnset;
use App\Console\Commands\FakeActivityUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DropReserveRoom;
use App\Console\Commands\DropForumBan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DropReserveRoom::class,
        DropForumBan::class,
        AdminSet::class,
        AdminUnset::class,
        FakeActivityUsers::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //$schedule->command('drop:reserve_room')->hourly();
        $schedule->command('crutch:images:rename')->hourly();
        $schedule->command('drop:forum_ban')->hourly();
        $schedule->command('clear:qr')->everyMinute();
        $schedule->command('refund:kaspi:import --transactions-pages=30')
                 ->timezone('Asia/Almaty')
                 ->dailyAt('9:00');
        $schedule->command('refund:kaspi:import')
                 ->timezone('Asia/Almaty')
                 ->between('9:20', '18:00')
                 ->everyFiveMinutes();
        $schedule->command('sro:pay:courses')->hourly();

        $schedule->command('debt_trust:refresh')
            ->timezone('Asia/Almaty')
            ->dailyAt('00:01');

        $schedule->command('user:balance:update')
            ->timezone('Asia/Almaty')
            ->dailyAt('02:00');

        $schedule->command('test1:check_end')
            ->timezone('Asia/Almaty')
            ->dailyAt('03:00');
//        $schedule->command('exam:check_end')
//            ->timezone('Asia/Almaty')
//            ->dailyAt('04:00');
//        $schedule->command('sro:check_end')
//            ->timezone('Asia/Almaty')
//            ->dailyAt('05:00');

        /*$schedule->command('buy_credits:to_limit')
            ->timezone('Asia/Almaty')
            ->dailyAt('02:00');*/
        $schedule->command('fake_activity')
            ->timezone('Asia/Almaty')
            ->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
