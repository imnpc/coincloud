<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\Recharge;
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

        try {
            $recharge = Recharge::find($this->recharge_id);
            $max = $recharge->coin;
            $orders = Order::where('wallet_type_id', $recharge->wallet_type_id)
                ->where('user_id', $recharge->user_id)
                ->where('is_pledge', 0)
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

                    $pledge = Pledge::create([
                        'user_id' => $v->user_id,
                        'order_id' => $v->id,
                        'product_id' => $v->product_id,
                        'wallet_type_id' => $v->wallet_type_id,
                        'power' => $v->number,
                        'pledge_fee' => $recharge->pledge_fee,
                        'pledge_coins' => $pledge_coins,
                        'pledge_days' => $v->product->pledge_days,
                        'gas_fee' => $recharge->gas_fee,
                        'gas_coins' => $gas_coins,
                    ]);
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
