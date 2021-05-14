<?php

namespace App\Jobs;

use App\Models\CloudWalletLog;
use App\Models\DayBonus;
use App\Models\DayFreed;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserBonus;
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
    public $timeout = 3000;

    /**
     * 任务描述
     * @var string
     */
    public $description = "执行每日分红"; // 完善中 TODO

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
            $logService = app()->make(LogService::class);
            //file_put_contents('./0_day_auto10.txt', $day);
            $bonus = DayBonus::where('day', '=', $day)
                ->where('status', '=', 0) // 未执行的
                ->first();// 查询前一天云算力系统分红记录
            if (!$bonus) {
                Log::info(__METHOD__ . '|任务已执行或者未找到对应日期记录');
                return;
            }

            $coin_add_day = $bonus->efficiency; // 当日每T产币量 = 挖矿效率

            // 按照用户处理 查询用户当前 有效的云算力  TODO
            $users = User::where('cloud_power', '>', 0)
                //->where('id', '=', 2) // TODO 需要去掉指定用户
                ->get(); // 获取用户列表
            // 推荐的云算力 按照整数 参与 TODO
            $product_id = 4;
            $product = Product::find($product_id);
            // 需要根据产品ID区分 TODO
            foreach ($users as $k => $v) {
                $checkmybonus = UserBonus::where('day', '=', $day)
                    ->where('user_id', '=', $v->id) // 未执行的
                    ->where('bonus_id', '=', $bonus->id) // 未执行的
                    ->where('type', '=', Order::TYPE_CLOUD) // 云算力
                    ->where('product_id', '=', $product_id)
                    ->first();// 查询前一天系统分红记录
                if ($checkmybonus) {
                    continue;
                }

                $parent1_balance = 0; // 一级推广金额
                $commission_balance = 0; // 推荐金额

                $other_fee = 0;
                $system_coin = 0;

//                $cloud_power_reward = floor($v->cloud_power_reward / 1); // 推荐云算力（只取整数）
//                $valid_power = $v->cloud_valid_power + $cloud_power_reward;// 实际有效算力 = 账户有效云算力 + 推荐云算力（只取整数）

                // 实际有效算力 = 账户有效云算力（产品ID为3 等待状态0-已生效 status 1-封装中）
                $lday = Carbon::now()->subDay();
                $valid_power = Order::where('wait_status', '=', '0')
                    ->where('user_id', '=', $v->id) // 未执行的
                    ->where('status', '=', '1')
                    ->where('product_id', '=', $product_id)
                    ->where('confirm_time', '<=', $lday)
                    ->sum('max_valid_power');

                if ($valid_power <= 0) {
                    continue;
                }
                //$cloud_power_reward = floor($v->cloud_power_affiliate2 / 1); // 推荐云算力（只取整数）
                $valid_power = $valid_power;// 实际有效算力 = 账户有效云算力

                $coin_add_day = $bonus->efficiency - $bonus->cost; // 当日每T产币量 = 挖矿效率 - 挖矿成本

                $coin = number_fixed($coin_add_day * $valid_power, 5); // 当日产币量 = 挖矿效率 * 当前实际有效算力

                //产品收益比率 TODO
                if ($product->returns_ratio < 100) {
                    $old_coin = $coin;
                    $coin = number_fixed($coin * $product->returns_ratio / 100);
                    // 分给系统 TODO
                    $system_coin = number_fixed($old_coin - $coin);

                    $remark_share = "合作挖矿 +" . $system_coin;
                    $logService->CloudWalletLog(0, 0, 0, $system_coin, $day, $v->user_id, CloudWalletLog::TYPE_SHARE, '', $remark_share);
                }

                if ($bonus->fee > 0) {
                    $other_fee = number_fixed($bonus->fee * $v->cloud_valid_power, 5); // 额外扣除FIL总数 = 每T额外扣除FIL * 用户可用总T数
                }

                if ($coin <= 0) {
                    continue; // 如果当日产币量小于等于0 跳出继续下一个
                }

                $parent1 = config('system.parent1'); // 1代推荐分成比例
                $parent2 = config('system.parent2'); // 2代推荐分成比例
                $parent1_balance = number_fixed($coin * $parent1 / 100, 5); // 1代推荐奖励
                $parent2_balance = number_fixed($coin * $parent2 / 100, 5); // 1代推荐奖励
                $parent1_uid = 0;
                $commission_balance = $parent1_balance; // 推荐金额

                // 1代：用户上级 ID 大于 0
                if ($v->parent_id > 0 && $parent1_balance > 0) {
                    $parent1_uid = $v->parent_id; // 1代推荐人用户 ID
                    $parent1_user = User::find($parent1_uid); // 1代推荐人用户信息
                    $commission_balance = $commission_balance - $parent1_balance; // 推荐剩余金额
                    //添加到用户余额 + 记录日志 filecoin_balance
                    $remark1 = "推荐分红1代";
                    $logService->userLog(User::BALANCE_FILECOIN, $parent1_user->id, $parent1_balance, $v->id, $day, UserWalletLog::FROM_COMMISSION, $remark1, 0, 0, 0, Order::TYPE_CLOUD);
                    // 2代：1代用户上级 ID 大于 0
                    if ($parent1_user->parent_id > 0 && $parent2_balance > 0) {
                        $parent2_uid = $parent1_user->parent_id; // 2代推荐人用户 ID
                        $parent2_user = User::find($parent2_uid); // 2代推荐人用户信息
                        $commission_balance = $commission_balance - $parent2_balance; // 推荐剩余金额
                        //添加到用户余额 + 记录日志 filecoin_balance
                        $remark2 = "推荐分红2代";
                        $logService->userLog(User::BALANCE_FILECOIN, $parent2_user->id, $parent2_balance, $parent1_user->id, $day, UserWalletLog::FROM_COMMISSION, $remark2, 0, 0, 0, Order::TYPE_CLOUD);
                    }
                }

                // 云算力系统钱包日志
                $risk = number_fixed($coin * config('cloud.risk') / 100, 5); // 风控账户
                $admin = number_fixed($coin * config('cloud.admin') / 100, 5); // 后台管理
                $service = number_fixed($coin * config('cloud.service') / 100, 5); // 技术服务
                $team_a = number_fixed($coin * config('cloud.team_a') / 100, 5); // 团队分红池A
                $team_b = number_fixed($coin * config('cloud.team_b') / 100, 5); // 团队分红池B
                $team_c = number_fixed($coin * config('cloud.team_c') / 100, 5); // 团队分红池C
                $remark_system = "每日分红";
                $logService->CloudSystemLog($team_a, $team_b, $team_c, $risk, $admin, $service, $commission_balance, $day, $v->id, $remark_system);

                // 个人收益
                $pay_customer_rate = config('system.pay_customer_rate'); // 每日收益比例
                $coin_user = number_fixed($coin * $pay_customer_rate / 100, 5);// 分配给矿工的 80%

                $rate_day = config('system.rate_day'); // 立即释放比例 25%
                $rate_freed = config('system.rate_freed'); // 线性释放比例 75%  180天
                $coin_rate_day = number_fixed($coin_user * $rate_day / 100, 5); // 立即释放数量
                $coin_freed = number_fixed($coin_user * $rate_freed / 100, 5); // 线性释放数量
                $coin_freed = number_fixed($coin_freed - $other_fee); // 线性释放数量 = 线性释放数量 -其他扣费 TODO
                $coin_freed_day = number_fixed($coin_freed / 180, 5); // 当日线性释放数量
                $already_coin = $coin_freed_day; // 已释放数量
                $wait_coin = $coin_freed - $coin_freed_day; // 等待释放数量

                // 每日日收益 10% 奖励产币给推荐人 TODO
                // 必须 cloud_power_affiliate1 cloud_power_affiliate2 任意一个大于1 todo
                $day_coin_all = $coin_rate_day + $coin_freed_day;
                $reward = config('cloud.reward');
                $parent1_day = number_fixed($day_coin_all * $reward / 100, 5); // 1代推荐奖励
                if ($v->parent_id > 0 && $parent1_day > 0) {
                    $parent1_uid = $v->parent_id; // 1代推荐人用户 ID
                    $parent1_user = User::find($parent1_uid); // 1代推荐人用户信息
                    if ($parent1_user->cloud_power_affiliate1 > 1 || $parent1_user->cloud_power_affiliate2 > 1) {
                        $remark_day = "奖励产币1代";
                        $logService->userLog(User::BALANCE_FILECOIN, $parent1_user->id, $parent1_day, $v->id, $day, UserWalletLog::FROM_REWARD_DAY, $remark_day, 0, 0, 0, Order::TYPE_CLOUD);
                    }
                }

                //线性释放其他数量总计 TODO
                $coin_freed_other = 0;
                $other_freeds = Freed::where('user_id', $v->id)
                    ->where('status', '=', 0)
                    ->where('type', '=', Order::TYPE_CLOUD) // 云算力
                    ->get(); // 查询该用户的线性释放列表
                if ($other_freeds) {
                    foreach ($other_freeds as $key => $value) {
                        // 查询今天是否已执行 避免重复执行 TODO
                        $check_other = DayFreed::where('day', '=', $day)
                            ->where('user_id', '=', $v->id) // 用户
                            ->where('freed_id', '=', $value->id) // 线性释放 ID
                            ->first();
                        if ($check_other) {
                            continue;
                        }

                        if ($value->already_day < $value->days) {
                            // 每日线性释放记录
                            $coin_freed_other += $value->coin_freed_day;
                            $already_day = $value->already_day + 1; // 最新释放天数
                            $day_freeds = DayFreed::create([
                                'user_id' => $v->id,
                                'freed_id' => $value->id,
                                'day' => $value->day,
                                'coin' => $value->coin_freed_day,
                                'today' => $already_day,
                            ]);
                            $data = [
                                'already_day' => $already_day, // 已释放天数
                                'already_coin' => $value->already_coin + $value->coin_freed_day, // 已释放数量
                                //'wait_coin' => $value->wait_coin - $value->coin_freed_day, // 等待释放数量
                                'wait_coin' => bcsub($value->wait_coin, $value->coin_freed_day, 5), // 等待释放数量
                            ];

                            $other_freeds[$key]->update($data);
                            if ($value->already_day + 1 == $value->days) {
                                $other_freeds[$key]->update(['status' => 1]);// 标记为释放完毕
                            }
                            //添加到用户余额 + 记录日志 filecoin_balance
                            $remark_freed = "每日线性释放(O)第" . $already_day . "天,释放" . $value->coin_freed_day;
                            $logService->userLog(User::BALANCE_FILECOIN, $v->id, $value->coin_freed_day, 0, $value->day, UserWalletLog::FROM_FREED75, $remark_freed, 0, $value->id, 0, Order::TYPE_CLOUD);
                        }
                    }
                }

                // 当日可分配产币量 = 立即释放数量 + 当日线性释放数量 + 线性释放其他数量总计
                $coin_day = $coin_rate_day + $coin_freed_day + $coin_freed_other;
                $balance = $coin_day; // 余额

                // 用户每日分成 ;
                $user_bonuses = UserBonus::create([
                    'day' => $day,
                    'user_id' => $v->id,
                    'bonus_id' => $bonus->id,
                    'yesterday_power' => 0,
                    'power_add' => 0,
                    'power' => $valid_power,
                    'coin_add' => $bonus->coin_add,
                    'coin_add_day' => $coin_add_day,
                    'coin' => $coin,
                    'max_valid_power' => $valid_power,
                    'coin_user' => $coin_user,
                    'rate_day' => $rate_day,
                    'rate_freed' => $rate_freed,
                    'coin_rate_day' => $coin_rate_day,
                    'coin_freed' => $coin_freed,
                    'coin_freed_day' => $coin_freed_day,
                    'coin_freed_other' => $coin_freed_other,
                    'coin_day' => $coin_day,
                    'pay_customer_rate' => $pay_customer_rate,
                    'balance' => $balance,
                    'parent1_balance' => $parent1_balance,
                    'parent1_uid' => $parent1_uid,
                    'parent1' => $parent1,
                    'bonus_rate' => config('cloud.team_a') + config('cloud.team_b') + config('cloud.team_c'),
                    'bonus_pool' => $team_a + $team_b + $team_c,
                    'fee' => $service,
                    'type' => Order::TYPE_CLOUD,
                    'product_id' => $product_id,
                    'returns_ratio' => $product->returns_ratio,
                    'system_coin' => $system_coin,
                ]);

                // 线性释放列表
                $freeds = Freed::create([
                    'user_id' => $v->id,
                    'user_bonus_id' => $user_bonuses->id,
                    'day' => $day,
                    'coin' => $coin_user,
                    'rate_freed' => $rate_freed,
                    'coin_freed' => $coin_freed,
                    'coin_freed_day' => $coin_freed_day,
                    'other_fee' => $other_fee,
                    'days' => 180,
                    'already_day' => 1,
                    'already_coin' => $already_coin,
                    'wait_coin' => $wait_coin,
                    'type' => Order::TYPE_CLOUD,
                    'product_id' => $product_id,
                ]);

                // 每日线性释放记录
                $day_freeds = DayFreed::create([
                    'user_id' => $v->id,
                    'freed_id' => $freeds->id,
                    'day' => $freeds->day,
                    'coin' => $freeds->coin_freed_day,
                    'today' => 1,
                ]);
                // 线性释放金额记录到用户日志 TODO
                $remark_day = "用户每日新增可用资产" . $coin_rate_day;
                $logService->userLog(User::BALANCE_FILECOIN, $v->id, $coin_rate_day, 0, $day, UserWalletLog::FROM_FREED, $remark_day, $user_bonuses->id, 0, 0, Order::TYPE_CLOUD);
                $remark_freed_first = "每日线性释放(F)第1天,释放" . $coin_freed_day;
                $logService->userLog(User::BALANCE_FILECOIN, $v->id, $coin_freed_day, 0, $day, UserWalletLog::FROM_FREED75, $remark_freed_first, 0, $freeds->id, 0, Order::TYPE_CLOUD);
            }
            // 标记云算力分红为已执行
            $bonus->update(['status' => 1]);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
