<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\Recharge;
use App\Models\UserWalletLog;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoCreatePledge implements ShouldQueue
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
    public $timeout = 3000;

    /**
     * 任务描述
     * @var string
     */
    public $description = "充值审核以后自动创建质押币"; // 完善中 TODO

    protected $recharge_id; // 产品ID

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recharge_id)
    {
        $this->recharge_id = $recharge_id;
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
        $logService = app()->make(LogService::class); // 钱包服务初始化 TODO

        try {
            $recharge = Recharge::find($this->recharge_id);
            $max = $recharge->coin;
            $orders = Order::where('wallet_type_id', $recharge->wallet_type_id)
                ->where('user_id', $recharge->user_id)
                ->where('product_id', $recharge->product_id)
                ->where('is_pledge', 0)
                ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
                ->where('status', '=', 0) // 订单状态 0-有效 1-无效
                ->get();
            if ($orders) {
                $used = 0;
                foreach ($orders as $k => $v) {
                    $pledge_coins = $v->number * $recharge->pledge_fee;
                    $gas_coins = $v->number * $recharge->gas_fee;
                    $total = $pledge_coins + $gas_coins;
                    $used += $total;
                    if ($used > $max) {
                        continue;
                    }

                    $product = Product::find($v->product_id); // 获取产品信息
                    $pledge_base = $v->number * $product->pledge_base;
                    $pledge_flow = $v->number * $product->pledge_flow;

                    $pledge = Pledge::create([
                        'user_id' => $v->user_id,
                        'order_id' => $v->id,
                        'product_id' => $v->product_id,
                        'wallet_type_id' => $v->wallet_type_id,
                        'power' => $v->number,
                        'pledge_fee' => $recharge->pledge_fee,
                        'pledge_coins' => $pledge_coins,
                        'pledge_days' => $v->product->pledge_days,
                        'wait_days' => $v->product->pledge_days,
                        'gas_fee' => $recharge->gas_fee,
                        'gas_coins' => $gas_coins,
                        'pledge_type' => $product->pledge_type,
                        'pledge_base' => $pledge_base,
                        'pledge_flow' => $pledge_flow,
                    ]);
                    // 质押币增加
//                    $remark = "质押币 + " . $pledge_coins;
//                    $logService->userLog($v->user_id, $v->wallet_type_id, $pledge_coins, 0, $day, UserWalletLog::FROM_PLEDGE, $remark);

                    // 更新订单字段 是否产生质押记录 is_pledge TODO
                    if ($pledge) {
                        $orders[$k]->update(['is_pledge' => 1]);
                    }
                }

            }
        } catch (\Exception $e) {
//            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e]);
        }
    }
}
