<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\RevisarEstacionamiento::class,
    ];
    protected function scheduleTimezone()
{
    return 'America/Argentina/Cordoba'; // Ajusta esto a tu zona horaria
}
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('estacionamiento:revisar')->everyMinute();
        // $schedule->command('inspire')->hourly();
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
