<?php

namespace App\Jobs;

use App\Models\DayBonus;
use App\Models\DefaultDayBonus;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoCreateDayBonus implements ShouldQueue
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
    public $description = "每天自动创建分红记录";

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
            $list = Product::all();
            foreach ($list as $k => $v) {
                $bonus = DayBonus::where('day', '=', $day)
                    ->where('product_id', '=', $v->id)
                    ->first(); // 查询前 1 天该产品系统分红记录
                if (!$bonus) {
                    $data = DefaultDayBonus::where('product_id', '=', $v->id)->first(); // 查询该商品默认数据
                    // 自动创建分红记录
                    DayBonus::create([
                        'product_id' => $v->id, // 产品
                        'day' => $day, // 日期
                        'total_power' => Order::where('wait_status', '=', 0)->where('status', '=', 0)->where('pay_status', '=', 0)
                            ->where('product_id', '=', $v->id)->sum('number'), // 有效算力总数
                        'power_add' => $data['power_add'], // 新增算力
                        'coin_add' => $data['coin_add'], // 产币数量
                        'efficiency' => $data['efficiency'], // 挖矿效率
                        'cost' => $data['cost'], // 挖矿成本
                        'fee' => $data['fee'], // 额外扣除
                        'day_price' => $data['day_price'], // 当天币价
                        'day_pledge' => $data['day_pledge'], // 当天质押币系数
                        'day_cost' => $data['day_cost'], // 当天单T封装成本
                        'remark' => '系统自动生成', // 备注
                    ]);
                }

                // 系统借币 TODO
//                $check_borrow = SystemBorrow::where('day', '=', $today)
//                    ->first();
//                if (!$check_borrow) {
//                    SystemBorrow::create([
//                        'day' => $today,
//                        'coin' => config('borrow.day_limit'),
//                        'interest' => config('borrow.interest'),
//                        'days' => config('borrow.days'),
//                    ]);
//                }
            }


        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
