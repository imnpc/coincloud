<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Pledge;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PledgeDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 5;

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
    public $description = "每天自动设置质押币";

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
        $now = Carbon::now();
        $day = Carbon::yesterday()->toDateString();// 获得日期
        $yesterday = Carbon::yesterday(); // 昨天

        try {
            $logService = app()->make(LogService::class); // 钱包服务初始化 TODO

            $orders = Order::where('pay_status', '=', Order::PAID_COMPLETE)
                ->get(); // 支付状态 0-已完成
            foreach ($orders as $k => $v) {
                $check_pledge = Pledge::where('user_id', '=', $v->user_id)
                    ->where('product_id', '=', $v->product_id)
                    ->where('order_id', '=', $v->id)
                    ->first();
                if (!$check_pledge) {
                    if ($v->product->pledge_fee > 0 || $v->product->gas_fee > 0) {
                        // 产品质押币封装模式为 0 的 才执行
                        if($v->product->package_type == 0){
                            Pledge::create([
                                'user_id' => $v->user_id,
                                'order_id' => $v->id,
                                'product_id' => $v->product_id,
                                'wallet_type_id' => $v->wallet_type_id,
                                'power' => $v->number,
                                'pledge_fee' => $v->product->pledge_fee,
                                'coins' => $v->number * $v->product->pledge_fee,
                                'pledge_days' => $v->product->pledge_days,
                                'gas_fee' => $v->product->gas_fee,
                                'gas_coins' => $v->number * $v->product->gas_fee,
                            ]);
                        }
                    }
                    continue;
                }
                if ($check_pledge->status == 1) {
                    continue;
                }
                if ($check_pledge->status == 0) {
                    // wait_days
                    $created_at = $check_pledge->created_at;
                    $check_day = $created_at->diffInDays($now); // 已经过去天数
                    $pledge_day = $check_pledge->pledge_days - $check_day;
                    if ($pledge_day <= 0) {
                        $pledge_day = 0;
                        // 需要修改 TODO
                        $remark_day = "质押币退回 " . $check_pledge->coins;
                        $logService->userLog($v->user_id, $v->wallet_type_id, $check_pledge->coins, 0, $day, UserWalletLog::FROM_PLEDGE, $remark_day);

                        $check_pledge->update(['wait_days' => $pledge_day, 'status' => 1]);
                    } else {
                        $check_pledge->update(['wait_days' => $pledge_day]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
