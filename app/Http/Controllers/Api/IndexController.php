<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\DayBonus;
use App\Models\DayFreed;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\UserWalletLog;
use App\Models\Weekly;
use App\Models\WeeklyLog;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        // Banner 图片
        $banner = [];
        $banner[] = config('app.banner1');
        $banner[] = config('app.banner2');
        $banner[] = config('app.banner3');
        $banner_list = [];
        foreach ($banner as $ban) {
            if ($ban) {
                $banner_list[] = Storage::disk('oss')->url($ban);
            } else {
                return '';
            }
        }
        $data['banner'] = array_values($banner_list);

        // 获取公告
        $announcement = Announcement::where('is_recommand', '=', 1)
            ->where('status', '=', 1)
            ->select('id', 'title')
            ->get();
        $data['announcement'] = $announcement;

        // 导航栏
        $nav = ArticleCategory::where('status', '=', 1)
            ->orderBy('order', 'asc')
            ->select('id', 'title', 'icon')
            ->get();
        $data['nav'] = $nav;

        // 矿池运营数据 TODO
        $list = Product::where('status', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        //print_r($list->toArray());
        foreach ($list as $k => $v) {
            //
            $data['coinlist'][$k]['name'] = $v->wallet_slug;
            $data['coinlist'][$k]['total_revenue'] = $v->total_revenue;
            $data['coinlist'][$k]['yesterday_revenue'] = $v->yesterday_revenue;
            $data['coinlist'][$k]['yesterday_gas'] = $v->yesterday_gas;
            $data['coinlist'][$k]['yesterday_efficiency'] = $v->yesterday_efficiency;
            $data['coinlist'][$k]['total_revenue_text'] = $v->total_revenue_text;
            $data['coinlist'][$k]['yesterday_revenue_text'] = $v->yesterday_revenue_text;
            $data['coinlist'][$k]['yesterday_gas_text'] = $v->yesterday_gas_text;
            $data['coinlist'][$k]['yesterday_efficiency_text'] = $v->yesterday_efficiency_text;
        }

        // 矿池总产量 total_revenue 昨日产量 yesterday_revenue  昨日消耗GAS yesterday_gas 挖矿效率 yesterday_efficiency
//        $data['filpool']['progress'] = config('filpool.progress'); // 矿池填充进度
//        $data['filpool']['node_power'] = config('filpool.node_power'); // 节点总有效算力
//        $data['filpool']['community_power'] = config('filpool.community_power'); // 社区总有效算力
//        $data['filpool']['community_torage_space'] = config('filpool.community_torage_space'); // 社区总存储空间
//        $data['filpool']['total_revenue'] = config('filpool.total_revenue'); // 矿池总收益
//        $data['filpool']['yesterday_revenue'] = config('filpool.yesterday_revenue'); // 昨日收益
//        $data['filpool']['yesterday_gas'] = config('filpool.yesterday_gas'); // 昨日消耗GAS
//        $data['filpool']['single_revenue'] = config('filpool.single_revenue'); // 有效算力单T收益

        // 产品列表
        $product = Product::where('status', '=', 0)
            ->orderBy('id', 'desc')
            ->select('id', 'name', 'thumb', 'choose_reason')
            ->get();

        $data['product'] = $product;

        return $data;
    }

    public function aboutus(Request $request)
    {
        $data = Article::where('article_category_id', '=', 7)->where('status', 1)->first();
        return $data;
    }

    public function test()
    {
        // 3 4 5
        $wallet_type_id = 5;
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
        echo $UserWalletService->walletTotal($wallet_type_id);
        exit();
        $orders = Order::where('pay_status', '=', Order::PAID_COMPLETE)
            ->get(); // 支付状态 0-已完成
        foreach ($orders as $k => $v) {
            $check_pledge = Pledge::where('user_id', '=', $v->user_id)
                ->where('product_id', '=', $v->product_id)
                ->where('order_id', '=', $v->id)
                ->first();
            if (!$check_pledge) {
                echo $v->product->pledge_fee;
                exit();
            }
        }
//        $id = 1;
//        $ids = User::unlimitedCollectionById($id);
////        $users_son1 = [];
////
////        foreach ($ids as $key => $value) {
////            $users_son1[] = $value['id'];
////        }
////        $users1 = array_values($users_son1);
//        print_r($ids);

        exit();
        $day = Carbon::yesterday()->toDateString();// 获得日期
        $today = Carbon::now()->toDateString();// 获得日期

        $logService = app()->make(LogService::class); // 钱包服务初始化 TODO
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
        $product_id = 1;
        $bonus = DayBonus::where('day', '=', $day)
            ->where('product_id', '=', $product_id) // 产品ID
            ->where('status', '=', 0) // 0-未执行 1-已执行
            ->first();// 查询前 1 天分红记录
        if (!$bonus) {
            Log::info(__METHOD__ . '|任务已执行或者未找到对应日期记录');
            return;
        }

        $lists = Order::where('product_id', $product_id)
            ->where('wait_status', '=', 0) // 等待状态 0-已生效 1-等待中
            ->where('pay_status', '=', 0) // 支付状态 0-已完成 1-未提交 2-审核中
            ->where('status', '=', 0) // 订单状态 0-有效 1-无效
            ->get(); // 获取本产品的有效订单列表
        //print_r($lists->toArray());
        $product = Product::find($product_id); // 获取产品信息

        // 按照每个订单购买的有效 T 数执行分红
        foreach ($lists as $k => $v) {
            $checkmybonus = UserBonus::where('day', '=', $day)
                ->where('user_id', '=', $v->user_id) // 未执行的
                ->where('day_bonus_id', '=', $bonus->id) // 未执行的
                ->where('product_id', '=', $product_id)
                ->first();// 查询用户分红记录是否存在

            if ($checkmybonus) {
                continue;
            }
            $coin_parent1 = 0; // 一级推广金额
            $coin_parent2 = 0; // 一级推广金额
            $commission_balance = 0; // 推荐金额

            $other_fee = 0;
            $system_coin = 0;

            $each_add = $bonus->efficiency - $bonus->cost; // 当日每T产币量 = 挖矿效率 - 挖矿成本
            $coins = number_fixed($each_add * $v->valid_power, 5); // 当日产币量 = 挖矿效率 * 订单实际有效算力

            if ($bonus->fee > 0) {
                $other_fee = number_fixed($bonus->fee * $v->valid_power, 5); // 额外扣除总数 = 每T额外扣除fee * 订单实际有效算力
            }

            if ($coins <= 0) {
                continue; // 如果当日产币量小于等于0 跳出继续下一个
            }

            $parent1_rate = $product->parent1_rate; // 1代推荐分成比例
            $parent2_rate = $product->parent2_rate; // 2代推荐分成比例
            $coin_parent1 = number_fixed($coins * $parent1_rate / 100, 5); // 1代推荐奖励
            $coin_parent2 = number_fixed($coins * $parent2_rate / 100, 5); // 2代推荐奖励
            $parent1_uid = 0; // 1代推荐人 UID
            $parent2_uid = 0; // 2代推荐人 UID
            $commission_balance = number_fixed($coin_parent1 + $coin_parent2); // 推荐剩余金额

            $user = User::find($v->user_id);

            // 1代：用户上级 ID 大于 0
            if ($user->parent_id > 0 && $coin_parent1 > 0) {
                $parent1_uid = $user->parent_id; // 1代推荐人用户 ID
                $parent1_user = User::find($parent1_uid); // 1代推荐人用户信息
                $commission_balance = number_fixed($commission_balance - $coin_parent1); // 推荐剩余金额
                //添加到用户余额 + 记录日志 filecoin_balance
                $remark1 = "推荐分红1代";
                $logService->userLog($parent1_uid, $product->wallet_type_id, $coin_parent1, $user->id, $day, UserWalletLog::FROM_COMMISSION, $remark1);
                // 2代：1代用户上级 ID 大于 0
                if ($parent1_user->parent_id > 0 && $coin_parent2 > 0) {
                    $parent2_uid = $parent1_user->parent_id; // 2代推荐人用户 ID
                    $parent2_user = User::find($parent2_uid); // 2代推荐人用户信息
                    $commission_balance = number_fixed($commission_balance - $coin_parent2); // 推荐剩余金额
                    //添加到用户余额 + 记录日志 filecoin_balance
                    $remark2 = "推荐分红2代";
                    $logService->userLog($parent2_uid, $product->wallet_type_id, $coin_parent2, $parent1_uid, $day, UserWalletLog::FROM_COMMISSION, $remark2);
                    // $UserWalletService
                }
            }

            // 云算力系统钱包日志
            $risk = number_fixed($coins * $product->risk_rate / 100, 5); // 风控池
            $team_a = number_fixed($coins * $product->bonus_team_a / 100, 5); // 分红池A
            $team_b = number_fixed($coins * $product->bonus_team_b / 100, 5); // 分红池B
            $team_c = number_fixed($coins * $product->bonus_team_c / 100, 5); // 分红池C

            $coin_risk = number_fixed($risk - $team_a - $team_b - $team_c); // 风控池实际金额 = 风控池 - 分红池A - 分红池B - 分红池C
            // TODO
            $remark_system = "每日分红";
//            $logService->CloudSystemLog($product_id, $risk, $team_a, $team_b, $team_c, $commission_balance, $day, $v->user_id, $remark_system);

            // 个人收益
            $pay_user_rate = $product->pay_user_rate; // 每日收益比例
            $coin_for_user = number_fixed($coins * $pay_user_rate / 100, 5);// 分配给矿工的 80%

            $now_rate = $product->now_rate; // 立即释放比例 25%
            $freed_rate = $product->freed_rate; // 线性释放比例 75%  180天
            $coin_now = number_fixed($coin_for_user * $now_rate / 100, 5); // 立即释放数量
            $coin_freed = number_fixed($coin_for_user * $freed_rate / 100, 5); // 线性释放数量
            $coin_freed = number_fixed($coin_freed - $other_fee); // 线性释放数量 = 线性释放数量 -其他扣费 TODO

            $coin_freed_day = number_fixed($coin_freed / $product->freed_days, 5); // 当日线性释放数量

            $already_coin = number_fixed($coin_freed_day); // 已释放数量
            $wait_coin = number_fixed($coin_freed - $coin_freed_day); // 等待释放数量
            if ($wait_coin < 0) {
                $wait_coin = 0; // 如果等待释放的数量小于0 标记为0 原因是:计算精度会有稍微差别 小数点最后2位可能会有问题
            }

            //线性释放其他数量总计 TODO
            $coin_freed_other = 0;
            $other_freeds = Freed::where('user_id', $v->user_id)
                ->where('status', '=', 0)
                ->where('product_id', '=', $product_id) // 产品 ID
                ->get(); // 查询该用户的线性释放列表
            if ($other_freeds) {
                foreach ($other_freeds as $key => $value) {
                    // 查询今天是否已执行
                    $check_other = DayFreed::where('day', '=', $day)
                        ->where('user_id', '=', $v->user_id) // 用户
                        ->where('freed_id', '=', $value->id) // 线性释放 ID
                        ->where('product_id', '=', $product_id) // 产品 ID
                        ->first();
                    if ($check_other) {
                        continue;
                    }

                    if ($value->already_day < $value->days) {
                        // 每日线性释放记录
                        $coin_freed_other += $value->coin_freed_day;
                        $already_day = $value->already_day + 1; // 最新释放天数
                        $day_freeds = DayFreed::create([
                            'user_id' => $v->user_id,
                            'freed_id' => $value->id,
                            'product_id' => $product_id,
                            'day' => $day,
                            'coin' => $value->coin_freed_day,
                            'today' => $already_day,
                        ]);

                        $data = [
                            'already_day' => $already_day, // 已释放天数
                            'already_coin' => number_fixed($value->already_coin + $value->coin_freed_day), // 已释放数量
                            'wait_coin' => number_fixed($value->coin_freed - $value->already_coin - $value->coin_freed_day), // 等待释放数量
                        ];

                        $other_freeds[$key]->update($data);
                        if ($value->already_day + 1 == $value->days) {
                            $other_freeds[$key]->update(['status' => 1]);// 标记为释放完毕
                        }
                        //添加到用户余额 + 记录日志 filecoin_balance TODO
                        $remark_freed = "每日线性释放(O)第" . $already_day . "天,释放" . $value->coin_freed_day;
                        $logService->userLog($v->user_id, $product->wallet_type_id, $value->coin_freed_day, 0, $value->day, UserWalletLog::FROM_FREED75, $remark_freed);
                    }
                }
            }

            // 当日可分配产币量 = 立即释放数量 + 当日线性释放数量 + 线性释放其他数量总计
            $coin_day = number_fixed($coin_now + $coin_freed_day + $coin_freed_other);
            $balance = $coin_day; // 余额

            // 用户每日分成 ;
            $user_bonuses = UserBonus::create([
                'day' => $day,
                'day_bonus_id' => $bonus->id,
                'user_id' => $v->user_id,
                'product_id' => $product_id,
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
            ]);

            // 线性释放金额记录到用户日志 TODO
            $remark_day = "用户每日新增可用资产" . $coin_now;
            $logService->userLog($v->user_id, $product->wallet_type_id, $coin_now, 0, $day, UserWalletLog::FROM_FREED, $remark_day);

            // 如果 线性释放比例大于 0
            if ($freed_rate > 0) {
                // 线性释放列表
                $freeds = Freed::create([
                    'user_id' => $v->user_id,
                    'user_bonus_id' => $user_bonuses->id,
                    'product_id' => $product_id,
                    'day' => $day,
                    'coins' => $coin_for_user,
                    'freed_rate' => $freed_rate,
                    'coin_freed' => $coin_freed,
                    'coin_freed_day' => $coin_freed_day,
                    'other_fee' => $other_fee,
                    'days' => $product->freed_days,
                    'already_day' => 1,
                    'already_coin' => $already_coin,
                    'wait_coin' => $wait_coin,
                ]);

                // 每日线性释放记录
                $day_freeds = DayFreed::create([
                    'user_id' => $v->user_id,
                    'freed_id' => $freeds->id,
                    'product_id' => $product_id,
                    'day' => $day,
                    'coin' => $freeds->coin_freed_day,
                    'today' => 1,
                ]);
                $remark_freed_first = "每日线性释放(F)第1天,释放" . $coin_freed_day;
                $logService->userLog($v->user_id, $product->wallet_type_id, $coin_freed_day, 0, $day, UserWalletLog::FROM_FREED75, $remark_freed_first);

            }
        }
    }

    public function demo()
    {

        $day = Carbon::yesterday()->toDateString();// 获得日期
        $today = Carbon::now()->toDateString();// 获得日期


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

            $each = number_fixed($v->max_valid_power * $v->package_rate / 100); // 每天封装数量
            $package_already = $v->package_already + $each; // 已封装数量
            $valid_power = $v->valid_power + $each; // 当前有效T数
            if ($package_already >= $v->max_valid_power) {
                $package_already = $v->max_valid_power;
            }
            if ($valid_power >= $v->max_valid_power) {
                $valid_power = $v->max_valid_power;
            }

            $package_wait = number_fixed($v->max_valid_power - $package_already); // 等待封装数量
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

exit();
        $product_id = 1;
        $this->product_id = $product_id;
        // 获取上周周数  日期
        $date = Carbon::now();
        // 只在周一执行 需要修改为 1
        if ($date->dayOfWeekIso == 6) {
            // 获得上周信息
            $lastweek = Carbon::now()->subWeek(3);

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
                $logs = UserWalletLog::where('add', '>', 0)
                    ->where('user_id', '=', $value['id'])
                    ->where('wallet_type_id', '=', $product->wallet_type_id)
                    ->whereBetween('created_at', [$begin_time, $end_time])
                    ->get()
                    ->toArray();
                print_r($logs);
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
    }
}
