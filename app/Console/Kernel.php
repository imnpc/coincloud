<?php

namespace App\Console;

use App\Jobs\AutoCreateDayBonus;
use App\Jobs\AutoProductBonus;
use App\Jobs\AutoProductWeeklyReport;
use App\Jobs\AutoUpdateCoinPrice;
use App\Jobs\ChangeOrderWaitStatus;
use App\Jobs\LevelAndTeam;
use App\Jobs\OrderPackage;
use App\Jobs\PledgeDay;
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
        // 本地测试

        // 服务器
        $schedule->command('config:cache')->dailyAt('23:50'); // 更新缓存
        $schedule->command('queue:restart')->dailyAt('23:51');// 重启队列
        $schedule->command('horizon:terminate')->dailyAt('23:53'); // 停止队列管理(一分钟以后自动重启队列)
        $schedule->command('currency:update -o')->everySixHours(); // 更新汇率 每隔6小时
        $schedule->command('geoip:update')->weekly()->thursdays()->at('4:30'); // 更新GEO地理位置数据库 每周四 4:30

        //$schedule->job(new AutoUpdateCoinPrice)->everyTenMinutes(); // 更新实时币价  服务器 10分钟一次
        $schedule->job(new AutoCreateDayBonus)->dailyAt('0:01'); // 每天自动创建分红记录 0:01
        //$schedule->job(new LevelAndTeam)->dailyAt('0:08'); // 设置会员级别和所属团队
        $schedule->job(new ChangeOrderWaitStatus)->dailyAt('0:20'); // 更改订单等待状态 0:20
        $schedule->job(new OrderPackage)->dailyAt('0:25'); // 订单封装有效算力 0:25
        $schedule->job(new AutoProductBonus)->dailyAt('0:05'); // 产品自动分红 0:05
        $schedule->job(new AutoProductWeeklyReport)->weekly()->mondays()->at('0:30'); // 产品每周自动统计报告 每周一 凌晨 0 点 30 执行
//        $schedule->job(new PledgeDay)->dailyAt('0:40'); // 自动处理质押币 0:40 TODO
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
