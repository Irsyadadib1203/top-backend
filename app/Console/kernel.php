<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('digiflazz:update-prices')
            ->everyFiveMinutes()
            ->withoutOverlapping();
        $schedule->job(new \App\Jobs\RetryPendingTransactions)
            ->everyFiveMinutes()
            ->withoutOverlapping();
        
    }

    protected $commands = [
        Commands\UpdatePricesFromDigiflazz::class,
    ];
}
