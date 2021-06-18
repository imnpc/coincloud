<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Models\Weekly;
use App\Models\WeeklyLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeeklyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * 任务描述
     * @var string
     */
    public $description = "每周统计报告";

    protected $product_id; // 产品ID

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $day = Carbon::now()->toDateString();
        try {
            // 获取上周周数  日期
            $date = Carbon::now();
            // 只在周一执行 需要修改为 1
            if ($date->dayOfWeekIso == 1) {
                // 获得上周信息
                $lastweek = Carbon::now()->subWeek();
                $year = $lastweek->startOfWeek()->year; // 按照该周开始日期确定所在年份 避免跨年问题
                $week = $lastweek->weekOfYear; // 上周周数

                $product = Product::find($this->product_id); // 获取产品信息

                //查询是否已经执行过了
                $check = Weekly::where('year', '=', $year)
                    ->where('week', '=', $week)
                    ->where('product_id', '=', $this->product_id)
                    ->first();
                if ($check) {
                    Log::info(__METHOD__ . '|生成数据报表任务已执行或者未找到对应日期记录');
                    return;
                }

                $begin = $lastweek->startOfWeek()->toDateString(); // 上周开始日期
                $end = $lastweek->endOfWeek()->toDateString(); // 上周结束日期
                $begin_time = $lastweek->startOfWeek()->toDateTimeString(); // 上周开始时间
                $end_time = $lastweek->endOfWeek()->toDateTimeString(); // 上周结束时间
                // 查询所有用户
                $users = User::all()->toArray();

                $freed = 0; // 25% 立即释放的
                $freed75 = 0; // 75% 线性释放的
                $reward = 0; // 奖励币
                $total = 0; // 总计

                //'product_id', 'wallet_type_id',
                // 预创建每周记录
                $weekly = Weekly::create([
                    'product_id' => $this->product_id,
                    'wallet_type_id' => $product->wallet_type_id,
                    'year' => $year,
                    'week' => $week,
                    'begin' => $begin,
                    'end' => $end,
                    'begin_time' => $begin_time,
                    'end_time' => $end_time,
                    'freed' => $freed,
                    'freed75' => $freed75,
                    'reward' => $reward,
                    'total' => $total,
                ]);
                // 循环用户 然后在其中循环钱包日志 将属于该用户的日志进行累加计算
                foreach ($users as $key => &$value) {
                    $value['freed'] = 0; // 25% 立即释放的
                    $value['freed75'] = 0; // 75% 线性释放的
                    $value['reward'] = 0; // 奖励币

                    // 查询上周用户钱包日志
                    $logs = UserWalletLog::where('type', '=', 1)
                        ->where('add', '>', 0)
                        ->where('user_id', '=', $value['id'])
                        ->whereBetween('created_at', [$begin_time, $end_time])
                        ->get()
                        ->toArray();
                    foreach ($logs as $k => $v) {
                        if ($v['user_id'] == $value['id']) {
                            if ($v['from'] == UserWalletLog::FROM_FREED) $value['freed'] += $v['add'];
                            if ($v['from'] == UserWalletLog::FROM_FREED75) $value['freed75'] += $v['add'];
                            if ($v['from'] == UserWalletLog::FROM_REWARD) $value['reward'] += $v['add'];
                        }
                    }
                    $value['total'] = $value['freed'] + $value['freed75'] + $value['reward'];
                    // 用户上周记录
                    WeeklyLog::create([
                        'product_id' => $weekly->product_id,
                        'wallet_type_id' => $weekly->wallet_type_id,
                        'user_id' => $value['id'],
                        'weekly_id' => $weekly->id,
                        'year' => $year,
                        'week' => $week,
                        'begin' => $begin,
                        'end' => $end,
                        'begin_time' => $begin_time,
                        'end_time' => $end_time,
                        'freed' => $value['freed'],
                        'freed75' => $value['freed75'],
                        'reward' => $value['reward'],
                        'total' => $value['total'],
                    ]);
                    // 系统上周数据
                    $freed += $value['freed'];
                    $freed75 += $value['freed75'];
                    $reward += $value['reward'];
                    $total += $value['total'];
                }

                // 更新系统上周数据
                $weekly->update([
                    'freed' => $freed,
                    'freed75' => $freed75,
                    'reward' => $reward,
                    'total' => $total,
                ]);

            }
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
