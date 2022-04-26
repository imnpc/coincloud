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

class OrderPackage implements ShouldQueue
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
    public $description = "订单封装有效算力";

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
            // 获取 封装状态 大于 0 的订单 封装状态 0-封装完成 1-等待封装 2-封装中
            $lists = Order::where('package_status', '>', 0)
                ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
                ->where('status', '=', 0) // 订单状态 0-有效 1-无效
                ->get();
            $now = Carbon::now()->toDateTimeString();
            foreach ($lists as $k => $v) {
                //  $table->decimal('max_valid_power', 32, 5)->comment('最大有效T数');
//            $table->decimal('package_rate', 8, 2)->default(0.00)->comment('封装比例');
//            $table->decimal('package_already', 32, 5)->comment('已封装数量');
//            $table->decimal('package_wait', 32, 5)->comment('等待封装数量');
//            $table->tinyInteger('package_status')->default(0)->comment('封装状态 0-封装完成 1-等待封装 2-封装中');

//                $each = @number_fixed($v->max_valid_power * $v->package_rate / 100); // 每天封装数量
                $each = @bcmul($v->max_valid_power, $v->package_rate / 100, 5); // 每天封装数量
//                $package_already = $v->package_already + $each; // 已封装数量
//                $valid_power = $v->valid_power + $each; // 当前有效T数
                $package_already = @bcadd($v->package_already, $each, 5); // 已封装数量
                $valid_power = @bcadd($v->valid_power, $each, 5); // 当前有效T数

                if ($package_already >= $v->max_valid_power) {
                    $package_already = $v->max_valid_power;
                }
                if ($valid_power >= $v->max_valid_power) {
                    $valid_power = $v->max_valid_power;
                }

//                $package_wait = number_fixed($v->max_valid_power - $package_already); // 等待封装数量
                $package_wait = @bcsub($v->max_valid_power, $package_already, 5); // 等待封装数量
                if ($package_wait <= 0) {
                    $package_wait = 0;
                    $package_status = 0;
                } else {
                    $package_status = 2;
                }

                $lists[$k]->update([
                    'package_status' => $package_status,
                    'valid_power' => $valid_power,
                    'package_already' => $package_already,
                    'package_wait' => $package_wait,
                ]); // 标记封装状态 封装完成
            }
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
