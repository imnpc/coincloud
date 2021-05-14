<?php

namespace App\Console;

use App\Jobs\AutoCreateDayBonus;
use App\Jobs\ChangeOrderWaitStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // 服务器
        $schedule->job(new AutoCreateDayBonus)->dailyAt('0:02'); // 每天自动创建分红记录
        $schedule->job(new ChangeOrderWaitStatus)->dailyAt('1:00'); // 更改订单等待状态
        // PowerBonusDaily
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
