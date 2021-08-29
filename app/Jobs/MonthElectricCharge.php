<?php

namespace App\Jobs;

use App\Models\ElectricCharge;
use App\Models\ElectricChargeLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonthElectricCharge implements ShouldQueue
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
    public $description = "每月创建电费账单";

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

        $lastmonth = Carbon::now()->subMonth();
        $year = $lastmonth->year;
        $month = $lastmonth->month;

        try {
            $list = Product::all();

            foreach ($list as $k => $v) {
                $check = ElectricCharge::where('product_id', '=', $v->id)
                    ->where('year', '=', $year)
                    ->where('month', '=', $month)
                    ->first();
                if ($check) {
                    $total_number = 0;
                    $total_fee = 0;
                    $users = Order::where('product_id', '=', $v->id)
                        ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
                        ->where('status', '=', 0) // 订单状态 0-有效 1-无效
                        ->get();
                    foreach ($users as $key => $value) {
                        $number = $value->number;
                        $electric_charge = $check->electric_charge;
                        $fee = $number * $electric_charge;
                        $total_number += $number;
                        $total_fee += $fee;

                        $check_my = ElectricChargeLog::where('user_id', '=', $value->user_id)
                            ->where('electric_charge_id', '=', $check->id)
                            ->where('product_id', '=', $value->product_id)
                            ->first();
                        if (!$check_my) {
                            ElectricChargeLog::create([
                                'user_id' => $value->user_id,
                                'electric_charge_id' => $check->id,
                                'product_id' => $value->product_id,
                                'wallet_type_id' => $value->wallet_type_id,
                                'year' => $year,
                                'month' => $month,
                                'electric_charge' => $check->electric_charge,
                                'number' => $value->number,
                                'total_fee' => $fee,
                            ]);
                        }
                    }

                    // 更新
                    $check->update([
                        'number' => $total_number,
                        'total_fee' => $total_fee,
                    ]);
                }

            }

        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
