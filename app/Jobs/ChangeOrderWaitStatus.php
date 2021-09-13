<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChangeOrderWaitStatus implements ShouldQueue
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
    public $description = "更改订单等待状态";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $day = Carbon::yesterday()->toDateString();// 获得日期
        $today = Carbon::now()->toDateString();// 获得日期

        try {
            // 获取等待天数大于 0 的订单
            $lists = Order::where('wait_days', '>', 0)
                ->where('wait_status', '=', 1) // 等待状态 0-已生效 1-等待中
                ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
                ->where('status', '=', 0) // 订单状态 0-有效 1-无效
                ->get();
            $now = Carbon::now()->toDateString();
            foreach ($lists as $k => $v) {
                // 按照 确认时间开始算
                if (is_null($v->confirm_time)) {
                    continue;
                }
                $beigin = $v->confirm_time->addDays($v->wait_days)->toDateString();

                if ($now >= $beigin) {
                    $lists[$k]->update(['wait_status' => 0]); // 标记等待状态 已生效
                }
            }
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
