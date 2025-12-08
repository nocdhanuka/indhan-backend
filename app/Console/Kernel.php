<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //Commands\Inspire::class,
        'App\Console\Commands\Inspire',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** Run every minute specified queue if not already started */
        // if (stripos((string) shell_exec('ps xf | grep \'[q]ueue:work\''), 'artisan queue:work') === false) {
        //     $schedule->command('queue:work --daemon --tries=1 --timeout=60')->everyMinute()->appendOutputTo(storage_path() . '/queue.log');
        // }

        //$schedule->command('inspire')->hourly();

        // $schedule->command('queue:work --daemon')
        //         ->appendOutputTo(storage_path() . '/queue.log')
        //         ->everyFiveMinutes()
        //         ->withoutOverlapping();
    }
}
