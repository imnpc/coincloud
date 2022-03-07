<?php

namespace App\Jobs;

use App\Models\DayBonus;
use App\Models\DayFreed;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\UserWalletLog;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoDayBonus implements ShouldQueue
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
    public $timeout = 7200;

    /**
     * 任务描述
     * @var string
     */
    public $description = "执行每日分红"; // 完善中 TODO

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
        $day = Carbon::yesterday()->toDateString();// 获得日期
        $today = Carbon::now()->toDateString();// 获得日期
        $now = Carbon::now()->toDateString();

        try {
            $logService = app()->make(LogService::class); // 钱包服务初始化 TODO
            $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化

            $bonus = DayBonus::where('day', '=', $day)
                ->where('product_id', '=', $this->product_id) // 产品ID
                ->where('status', '=', 0) // 0-未执行 1-已执行
                ->first();// 查询前 1 天分红记录
            if (!$bonus) {
                Log::info(__METHOD__ . '|任务已执行或者未找到对应日期记录');
                return;
            }

            $lists = Order::where('product_id', $this->product_id)
                ->where('wait_status', '=', 0) // 等待状态 0-已生效 1-等待中
                ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
                ->where('status', '=', 0) // 订单状态 0-有效 1-无效
                ->get(); // 获取本产品的有效订单列表

            $product = Product::find($this->product_id); // 获取产品信息

            // 按照每个订单购买的有效 T 数执行分红
            foreach ($lists as $k => $v) {
                $checkmybonus = UserBonus::where('day', '=', $day)
                    ->where('user_id', '=', $v->user_id) // 未执行的
                    ->where('day_bonus_id', '=', $bonus->id) // 未执行的
                    ->where('product_id', '=', $this->product_id)
                    ->where('order_id', $v->id)
                    ->first();// 查询用户分红记录是否存在
                if ($checkmybonus) {
                    continue;
                }

                // $product->package_type == 1 如果没有质押币 就不产币 TODO
                if ($product->package_type == 1) {
                    $pledge = Pledge::where('user_id', '=', $v->user_id)
                        ->where('product_id', $this->product_id)
                        ->where('order_id', $v->id)
                        ->first();
                    if (!$pledge) {
                        continue;
                    }
                }

                // 查询当前日期是否大于有效天数  超过天数不产币
                $begin = $v->confirm_time->addDays($v->valid_days)->toDateString();
                if ($now > $begin) {
                    continue;
                }

                $coin_parent1 = 0; // 一级推广金额
                $coin_parent2 = 0; // 一级推广金额
                $commission_balance = 0; // 推荐金额

                $other_fee = 0;
                $system_coin = 0;

//                $each_add = number_fixed($bonus->efficiency - $bonus->cost); // 当日每T产币量 = 挖矿效率 - 挖矿成本
//                $coins = number_fixed($each_add * $v->valid_power, 5); // 当日产币量 = 挖矿效率 * 订单实际有效算力
                $each_add = bcsub($bonus->efficiency, $bonus->cost, 5); // 当日每T产币量 = 挖矿效率 - 挖矿成本
                $coins = bcmul($each_add, $v->valid_power, 5); // 当日产币量 = 挖矿效率 * 订单实际有效算力

                if ($bonus->fee > 0) {
//                    $other_fee = number_fixed($bonus->fee * $v->valid_power, 5); // 额外扣除总数 = 每T额外扣除fee * 订单实际有效算力
                    $other_fee = bcmul($bonus->fee, $v->valid_power, 5); // 额外扣除总数 = 每T额外扣除fee * 订单实际有效算力
                }

                if ($coins <= 0) {
                    continue; // 如果当日产币量小于等于0 跳出继续下一个
                }

                // revenue_type  收益类型 0-默认  1-pledge 收满质押币
                // revenue_status  0-默认无需执行  1-执行中
                // 处理需要封装满质押币的 TODO
                if ($v->revenue_type == 1 && $v->revenue_status == 1) {
                    $parent1_rate = 0; // 1代推荐分成比例
                    $parent2_rate = 0; // 2代推荐分成比例
                    $coin_parent1 = 0; // 1代推荐奖励
                    $coin_parent2 = 0; // 2代推荐奖励
                    $parent1_uid = 0;
                    $parent2_uid = 0;
                    $risk = 0; // 风控池
                    $team_a = 0; // 分红池A
                    $team_b = 0; // 分红池B
                    $team_c = 0; // 分红池C
                    $coin_risk = 0;
                    // 个人收益
                    $pay_user_rate = 100; // 每日收益比例
                    $coin_for_user = $coins;// 分配给矿工的 100%
                    $now_rate = $pay_user_rate; // 立即释放比例
                    $freed_rate = 0; // 线性释放比例
                    $coin_now = $coin_for_user; // 立即释放数量
                    $coin_freed = 0; // 线性释放数量 = 线性释放数量 -其他扣费
                    $coin_freed_day = 0; // 当日线性释放数量
                    $need_coin_freed_day =0;
                    $already_coin = 0; // 已释放数量
                    $wait_coin = 0; // 等待释放数量

                    // 如果满足金额以后  标记 revenue_type 0  revenue_status TODO
                    $coins_total = UserBonus::where('user_id', '=', $v->user_id)
                        ->where('product_id', $this->product_id)
                        ->where('order_id', $v->id)
                        ->sum('coin_for_user'); // 本订单累计产出
                    $now_total = bcadd($coins_total, $coin_for_user, 5); // 本订单总产币数量 = 历史产出  + 今日产出
                    $price = huobiusdt(strtolower($product->wallet_slug), false); // 获取当前单币价格(美元转人民币)
                    if ($price > 0) {
//                        $money = number_fixed($price * $now_total); // 本订单获得的虚拟货币的人民币总价值 = 单币价格 * 数量
                        $money = bcmul($price, $now_total, 5); // 本订单获得的虚拟货币的人民币总价值 = 单币价格 * 数量
                        if ($money >= $v->pay_money) {
                            $lists[$k]->update([
                                'revenue_type' => 0,
                                'revenue_status' => 0,
                            ]); // 标记订单为默认
                        }
                    }
                } else {
                    $parent1_rate = $product->parent1_rate; // 1代推荐分成比例
                    $parent2_rate = $product->parent2_rate; // 2代推荐分成比例
//                $coin_parent1 = @number_fixed($coins * $parent1_rate / 100, 5); // 1代推荐奖励
//                $coin_parent2 = @number_fixed($coins * $parent2_rate / 100, 5); // 2代推荐奖励
                    $coin_parent1 = @bcmul($coins, $parent1_rate / 100, 5); // 1代推荐奖励
                    $coin_parent2 = @bcmul($coins, $parent2_rate / 100, 5); // 2代推荐奖励
                    $parent1_uid = 0; // 1代推荐人 UID
                    $parent2_uid = 0; // 2代推荐人 UID
//                $commission_balance = number_fixed($coin_parent1 + $coin_parent2); // 推荐剩余金额
                    $commission_balance = bcadd($coin_parent1, $coin_parent2, 5); // 推荐剩余金额

                    $coin_parent1_balance = 0; // 1代推荐奖励
                    $coin_parent2_balance = 0; // 2代推荐奖励

                    $user = User::find($v->user_id);

                    // 极差分红
                    //$model = new \App\Models\User(); //实例化model
                    //$model->JiCha($product->wallet_type_id, $v->user_id, $coins, $user->level_id);

                    // 1代：用户上级 ID 大于 0
                    if ($user->parent_id > 0 && $coin_parent1 > 0) {
                        $parent1_uid = $user->parent_id; // 1代推荐人用户 ID
                        $parent1_user = User::find($parent1_uid); // 1代推荐人用户信息
                        $coin_parent1_balance = $coin_parent1;
//                    $commission_balance = number_fixed($commission_balance - $coin_parent1); // 推荐剩余金额
                        $commission_balance = bcsub($commission_balance, $coin_parent1, 5); // 推荐剩余金额
                        //添加到用户余额 + 记录日志 filecoin_balance
                        $remark1 = "推荐分红1代";
                        $logService->userLog($parent1_uid, $product->wallet_type_id, $coin_parent1, $user->id, $day, UserWalletLog::FROM_COMMISSION, $remark1, $this->product_id, $v->id);
                        // 2代：1代用户上级 ID 大于 0
                        if ($parent1_user->parent_id > 0 && $coin_parent2 > 0) {
                            $parent2_uid = $parent1_user->parent_id; // 2代推荐人用户 ID
                            $parent2_user = User::find($parent2_uid); // 2代推荐人用户信息
                            $coin_parent2_balance = $coin_parent2;
//                        $commission_balance = number_fixed($commission_balance - $coin_parent2); // 推荐剩余金额
                            $commission_balance = bcsub($commission_balance, $coin_parent2, 5); // 推荐剩余金额
                            //添加到用户余额 + 记录日志 filecoin_balance
                            $remark2 = "推荐分红2代";
                            $logService->userLog($parent2_uid, $product->wallet_type_id, $coin_parent2, $parent1_uid, $day, UserWalletLog::FROM_COMMISSION, $remark2, $this->product_id, $v->id);
                        }
                    }

                    // 公司服务费 service_rate 2021-07-27 TODO
//                $service_fee = @number_fixed($coins * $product->service_rate / 100, 5); // 公司服务费
                    $service_fee = @bcmul($coins, $product->service_rate / 100, 5); // 公司服务费

                    // 云算力系统钱包日志
//                $risk = @number_fixed($coins * $product->risk_rate / 100, 5); // 风控池
//                $team_a = @number_fixed($coins * $product->bonus_team_a / 100, 5); // 分红池A
//                $team_b = @number_fixed($coins * $product->bonus_team_b / 100, 5); // 分红池B
//                $team_c = @number_fixed($coins * $product->bonus_team_c / 100, 5); // 分红池C

                    $risk = @bcmul($coins, $product->risk_rate / 100, 5); // 风控池
                    $team_a = @bcmul($coins, $product->bonus_team_a / 100, 5); // 分红池A
                    $team_b = @bcmul($coins, $product->bonus_team_b / 100, 5); // 分红池B
                    $team_c = @bcmul($coins, $product->bonus_team_c / 100, 5); // 分红池C
                    $coin_risk = number_fixed($risk - $team_a - $team_b - $team_c - $coin_parent1_balance - $coin_parent2_balance); // 风控池实际金额 = 风控池 - 分红池A - 分红池B - 分红池C - 1代分红 - 2代分红
                    // 系统钱包记录需要单独写 TODO
                    $remark_system = "系统每日分红";
                    $logService->SystemLog($product->wallet_type_id, $team_a, $team_b, $team_c, $risk, $commission_balance, $service_fee, $day, $v->user_id, $remark_system, $this->product_id, $v->id);

                    // 个人收益
                    $pay_user_rate = $product->pay_user_rate; // 每日收益比例
//                $coin_for_user = @number_fixed($coins * $pay_user_rate / 100, 5);// 分配给矿工的 80%
                    $coin_for_user = @bcmul($coins, $pay_user_rate / 100, 5);// 分配给矿工的 80%

                    $now_rate = $product->now_rate; // 立即释放比例 25%
                    $freed_rate = $product->freed_rate; // 线性释放比例 75%  180天
//                $coin_now = @number_fixed($coin_for_user * $now_rate / 100, 5); // 立即释放数量
//                $coin_freed = @number_fixed($coin_for_user * $freed_rate / 100, 5); // 线性释放数量
//                $coin_freed = number_fixed($coin_freed - $other_fee); // 线性释放数量 = 线性释放数量 -其他扣费 TODO
//                $coin_freed_day = @number_fixed($coin_freed / $product->freed_days, 5); // 当日线性释放数量
                    $coin_now = @bcmul($coin_for_user, $now_rate / 100, 5); // 立即释放数量
                    $coin_freed = @bcmul($coin_for_user, $freed_rate / 100, 5); // 线性释放数量
                    $coin_freed = @bcsub($coin_freed, $other_fee, 5); // 线性释放数量 = 线性释放数量 -其他扣费 TODO
                    $coin_freed_day = @bcdiv($coin_freed, $product->freed_days, 5); // 当日线性释放数量
                    if(!$coin_freed_day){
                        $coin_freed_day = 0;
                    }
                    $already_coin = @number_fixed($coin_freed_day); // 已释放数量
                    $need_coin_freed_day = @number_fixed($coin_freed_day); // 已释放数量
                    $already_day = 1;
                    // 如果产品 释放等待天数大于 0
                    if ($product->freed_wait_days > 0) {
                        $already_day = 0;
                        $already_coin = 0;
                        $coin_freed_day = 0;
                    }

//                $wait_coin = number_fixed($coin_freed - $coin_freed_day); // 等待释放数量
                    $wait_coin = bcsub($coin_freed, $coin_freed_day, 5); // 等待释放数量

                    if ($wait_coin < 0) {
                        $wait_coin = 0; // 如果等待释放的数量小于0 标记为0 原因是:计算精度会有稍微差别 小数点最后2位可能会有问题
                    }
                }

                //线性释放其他数量总计 TODO
                $coin_freed_other = 0;
                $other_freeds = Freed::where('user_id', $v->user_id)
                    ->where('status', '=', 0)
                    ->where('product_id', '=', $this->product_id) // 产品 ID
                    ->get(); // 查询该用户的线性释放列表
                if ($other_freeds) {
                    foreach ($other_freeds as $key => $value) {
                        // 查询是否在额外等待期 TODO
                        if ($value->freed_wait_days > 0) {
                            $wait = $value->created_at->addDays($value->freed_wait_days)->toDateString();
                            if ($now < $wait) {
                                continue;
                            }
                        }
                        // 需要处理等待周期的 没到时间自动跳过
                        // 查询今天是否已执行
                        $check_other = DayFreed::where('day', '=', $day)
                            ->where('user_id', '=', $v->user_id) // 用户
                            ->where('freed_id', '=', $value->id) // 线性释放 ID
                            ->where('product_id', '=', $this->product_id) // 产品 ID
                            ->first();
                        if ($check_other) {
                            continue;
                        }

                        if ($value->already_day < $value->days) {
                            // 每日线性释放记录
                            $coin_freed_other += $value->coin_freed_day;
                            $already = $value->already_day + 1; // 最新释放天数
                            $day_freeds = DayFreed::create([
                                'user_id' => $v->user_id,
                                'freed_id' => $value->id,
                                'product_id' => $this->product_id,
                                'day' => $day,
                                'coin' => $value->coin_freed_day,
                                'today' => $already,
                            ]);

                            $o_already_coin = bcadd($value->already_coin, $value->coin_freed_day, 5);
                            $o_wait_coin = bcsub($value->coin_freed, $value->already_coin, 5);
                            $o_wait_coin = bcsub($o_wait_coin, $value->coin_freed_day, 5);
                            $data = [
                                'already_day' => $already, // 已释放天数
//                                'already_coin' => number_fixed($value->already_coin + $value->coin_freed_day), // 已释放数量
//                                'wait_coin' => number_fixed($value->coin_freed - $value->already_coin - $value->coin_freed_day), // 等待释放数量
                                'already_coin' => $o_already_coin, // 已释放数量
                                'wait_coin' => $o_wait_coin, // 等待释放数量
                            ];

                            $other_freeds[$key]->update($data);
                            if ($already == $value->days) {
                                $other_freeds[$key]->update(['status' => 1]);// 标记为释放完毕
                            }
                            //添加到用户余额 + 记录日志 filecoin_balance TODO
                            $remark_freed = "每日线性释放(O)第" . $already . "天,释放" . $value->coin_freed_day;
                            $logService->userLog($v->user_id, $product->wallet_type_id, $value->coin_freed_day, 0, $value->day, UserWalletLog::FROM_FREED75, $remark_freed, $this->product_id, $v->id);
                        }
                    }
                }

                // 当日可分配产币量 = 立即释放数量 + 当日线性释放数量 + 线性释放其他数量总计
//                $coin_day = number_fixed($coin_now + $coin_freed_day + $coin_freed_other);
                $coin_day = bcadd($coin_now, $coin_freed_day, 5);
                $coin_day = bcadd($coin_day, $coin_freed_other, 5);
                $balance = $coin_day; // 余额

                // 用户每日分成
                $user_bonuses = UserBonus::create([
                    'day' => $day,
                    'day_bonus_id' => $bonus->id,
                    'user_id' => $v->user_id,
                    'product_id' => $this->product_id,
                    'bonus_coin_add' => $bonus->coin_add,
                    'valid_power' => $v->valid_power,
                    'each_add' => $each_add,
                    'coins' => $coins,
                    'pay_user_rate' => $product->pay_user_rate,
                    'coin_for_user' => $coin_for_user,
                    'now_rate' => $now_rate,
                    'coin_now' => $coin_now,
                    'freed_rate' => $freed_rate,
                    'coin_freed' => $coin_freed,
                    'coin_freed_day' => $coin_freed_day,
                    'coin_freed_other' => $coin_freed_other,
                    'coin_day' => $coin_day,
                    'balance' => $balance,
                    'parent1_uid' => $parent1_uid,
                    'parent1_rate' => $parent1_rate,
                    'coin_parent1' => $coin_parent1,
                    'parent2_uid' => $parent2_uid,
                    'parent2_rate' => $parent2_rate,
                    'coin_parent2' => $coin_parent2,
                    'bonus_rate' => $product->bonus_team_a + $product->bonus_team_b + $product->bonus_team_c,
                    'coin_bonus' => number_fixed($team_a + $team_b + $team_c),
                    'risk_rate' => $product->risk_rate,
                    'coin_risk' => $coin_risk,
                    'status' => 1, // 0-未执行 1-已执行
                    'order_id' => $v->id, // 订单 ID
                ]);

                // 线性释放金额记录到用户日志 TODO
                if ($coin_now > 0) {
                    $remark_day = "用户每日新增可用资产" . $coin_now;
                $logService->userLog($v->user_id, $product->wallet_type_id, $coin_now, 0, $day, UserWalletLog::FROM_FREED, $remark_day, $this->product_id, $v->id);
                }
                // 如果 线性释放比例大于 0 && 释放金额大于 0
                if ($freed_rate > 0 && $coin_freed > 0) {
                    // 线性释放列表
                    $freeds = Freed::create([
                        'user_id' => $v->user_id,
                        'user_bonus_id' => $user_bonuses->id,
                        'product_id' => $this->product_id,
                        'day' => $day,
                        'coins' => $coin_for_user,
                        'freed_rate' => $freed_rate,
                        'coin_freed' => $coin_freed,
                        'coin_freed_day' => $need_coin_freed_day,
                        'other_fee' => $other_fee,
                        'days' => $product->freed_days,
                        'already_day' => $already_day,
                        'already_coin' => $already_coin,
                        'wait_coin' => $wait_coin,
                        'freed_wait_days' => $product->freed_wait_days,
                    ]);

                    // 每日线性释放记录
                    if ($freeds->freed_wait_days == 0) {
                        $day_freeds = DayFreed::create([
                            'user_id' => $v->user_id,
                            'freed_id' => $freeds->id,
                            'product_id' => $this->product_id,
                            'day' => $day,
                            'coin' => $freeds->coin_freed_day,
                            'today' => 1,
                        ]);
                        $remark_freed_first = "每日线性释放(F)第1天,释放" . $coin_freed_day;
                    $logService->userLog($v->user_id, $product->wallet_type_id, $coin_freed_day, 0, $day, UserWalletLog::FROM_FREED75, $remark_freed_first, $this->product_id, $v->id);
                    }
                }
            }
            // 标记云算力分红为已执行
            $bonus->update(['status' => 1]);
        } catch (\Exception $e) {
//            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e]);
        }
    }
}
