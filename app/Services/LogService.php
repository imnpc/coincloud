<?php

namespace App\Services;

use App\Models\CloudWallet;
use App\Models\CloudWalletLog;
use App\Models\SystemWallet;
use App\Models\SystemWalletLog;
use App\Models\CloudSystemWallet;
use App\Models\CloudSystemWalletLog;
use App\Models\User;
use App\Models\UserWalletLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogService
{
    /**
     * 用户钱包日志 更新用户余额参数 TODO
     * @param int $type 操作类型 0-现金 1-FIL币 2-抵押的FIL币 3-奖励币 4-股东分红 5-云算力 6-云算力奖励 7-累计推荐下级购买云算力T数 8-有效云算力
     * @param int $uid 用户ID
     * @param float $add 更改金额 支持加减  + -
     * @param int $from_uid 来自用户 ID
     * @param int $day 所属日期
     * @param int $from 来源 0-正常 1-推荐 2-股东分红 3-转入 4-转出 5-线性释放 6-每日分红 7-奖励币 8-提币 9-提现  10-借币
     * @param string $remark 备注
     * @param int $user_bonus_id 用户分红记录 ID
     * @param int $freed_id 用户线性释放记录 ID
     * @param int $borrow_id 借币记录 ID
     * @param int $product_type 产品类型 0-矿机 1-云算力
     */

    public function userLog(int $type, int $uid, float $add, $from_uid = 0, $day = 0, $from = 0, $remark = '', $user_bonus_id = 0, $freed_id = 0, $borrow_id = 0, $product_type = 0)
    {
        $user = User::find($uid);

        UserWalletLog::create([
            'day' => $day,
            'type' => $type,
            'product_type' => $product_type,
            'user_id' => $user->id,
            'from_user_id' => $from_uid,
            'old' => $old,
            'add' => $add,
            'new' => $new,
            'from' => $from,
            'remark' => $remark,
            'user_bonus_id' => $user_bonus_id,
            'freed_id' => $freed_id,
            'borrow_id' => $borrow_id,
        ]);
    }

    /**
     * 系统钱包日志 更新系统钱包余额
     * @param int $bonus_pool 分红池
     * @param int $fee 托管费
     * @param int $commission_balance 推荐分红
     * @param int $day 所属日期
     * @param int $from_uid 来自用户ID
     * @param string $remark 备注
     */

    public function systemWalletLog($bonus_pool = 0, $fee = 0, $commission_balance = 0, $day = 0, $from_uid = 0, $remark = '')
    {
        $wallet = SystemWallet::find('1');
        $new_bonus_pool = $wallet->bonus_pool + $bonus_pool;
        $new_fee = $wallet->fee + $fee;
        $new_commission_balance = $wallet->commission_balance + $commission_balance;

        $wallet_log = SystemWalletLog::create([
            'day' => $day,
            'old_bonus_pool' => $wallet->bonus_pool,
            'old_fee' => $wallet->fee,
            'old_commission_balance' => $wallet->commission_balance,
            'bonus_pool_add' => $bonus_pool,
            'fee_add' => $fee,
            'commission_balance_add' => $commission_balance,
            'bonus_pool' => $new_bonus_pool,
            'fee' => $new_fee,
            'commission_balance' => $new_commission_balance,
            'from_user_id' => $from_uid,
            'remark' => $remark,
        ]);
        $wallet->update([
            'bonus_pool' => $new_bonus_pool,
            'fee' => $new_fee,
            'commission_balance' => $new_commission_balance,
        ]);
    }

    /**
     * 云算力-风控池日志 更新系统钱包余额
     * @param int $cost 挖矿成本
     * @param int $pledge_coin 质押币
     * @param int $bonus_pool 分红池
     * @param int $share 合作挖矿
     * @param int $day 所属日期
     * @param int $from_uid 来自用户ID
     * @param int $type 类型 0-默认 1-挖矿成本 2-质押币 3-分红池 4-合作挖矿 共享矿机（share）
     * @param int $order_id 订单ID
     * @param string $remark 备注
     */
    public function CloudWalletLog($cost = 0, $pledge_coin = 0, $bonus_pool = 0, $share = 0, $day = 0, $from_uid = 0, $type = 0, $order_id = 0, $remark = '')
    {
        $wallet = CloudWallet::find('1');
        $new_cost = $wallet->cost + $cost;
        $new_pledge_coin = $wallet->pledge_coin + $pledge_coin;
        $new_bonus_pool = $wallet->bonus_pool + $bonus_pool;
        $new_share = $wallet->share + $share;

        $wallet_log = CloudWalletLog::create([
            'day' => $day,
            'old_cost' => $wallet->cost,
            'old_pledge_coin' => $wallet->pledge_coin,
            'old_bonus_pool' => $wallet->bonus_pool,
            'old_share' => $wallet->share,
            'cost_add' => $cost,
            'pledge_coin_add' => $pledge_coin,
            'bonus_pool_add' => $bonus_pool,
            'share_add' => $share,
            'cost' => $new_cost,
            'pledge_coin' => $new_pledge_coin,
            'bonus_pool' => $new_bonus_pool,
            'share' => $new_share,
            'from_user_id' => $from_uid,
            'order_id' => $order_id,
            'remark' => $remark,
            'type' => $type,
        ]);
        $wallet->update([
            'cost' => $new_cost,
            'pledge_coin' => $new_pledge_coin,
            'bonus_pool' => $new_bonus_pool,
            'share' => $new_share,
        ]);
    }

    /**
     * @param int $team_a 分红池A
     * @param int $team_b 分红池B
     * @param int $team_c 分红池C
     * @param int $risk 风控账户
     * @param int $admin 后台管理
     * @param int $service 技术服务
     * @param int $commission_balance 推荐
     * @param int $day 天数
     * @param int $from_uid 来自用户ID
     * @param string $remark 备注
     */
    public function CloudSystemLog($team_a = 0, $team_b = 0, $team_c = 0, $risk = 0, $admin = 0, $service = 0, $commission_balance = 0, $day = 0, $from_uid = 0, $remark = '')
    {
        $wallet = CloudSystemWallet::find('1');
        $new_team_a = $wallet->team_a + $team_a;
        $new_team_b = $wallet->team_b + $team_b;
        $new_team_c = $wallet->team_c + $team_c;
        $new_risk = $wallet->risk + $risk;
        $new_admin = $wallet->admin + $admin;
        $new_service = $wallet->service + $service;
        $new_commission_balance = $wallet->commission_balance + $commission_balance;

        $wallet_log = CloudSystemWalletLog::create([
            'day' => $day,
            'old_team_a' => $wallet->team_a,
            'old_team_b' => $wallet->team_b,
            'old_team_c' => $wallet->team_c,
            'old_risk' => $wallet->risk,
            'old_admin' => $wallet->admin,
            'old_service' => $wallet->service,
            'old_commission_balance' => $wallet->commission_balance,

            'team_a_add' => $team_a,
            'team_b_add' => $team_b,
            'team_c_add' => $team_c,
            'risk_add' => $risk,
            'admin_add' => $admin,
            'service_add' => $service,
            'commission_balance_add' => $commission_balance,

            'team_a' => $new_team_a,
            'team_b' => $new_team_b,
            'team_c' => $new_team_c,
            'risk' => $new_risk,
            'admin' => $new_admin,
            'service' => $new_service,
            'commission_balance' => $new_commission_balance,

            'from_user_id' => $from_uid,
            'remark' => $remark,
        ]);
        $wallet->update([
            'team_a' => $new_team_a,
            'team_b' => $new_team_b,
            'team_c' => $new_team_c,
            'risk' => $new_risk,
            'admin' => $new_admin,
            'service' => $new_service,
            'commission_balance' => $new_commission_balance,
        ]);
    }
}
