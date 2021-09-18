<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SystemWallet;
use App\Models\SystemWalletLog;
use App\Models\UserWalletLog;
use Illuminate\Http\Request;

class LogService
{
    /**
     * @param int $uid 用户ID
     * @param int $wallet_type_id 钱包类型 ID
     * @param $add 更改金额 支持加减  + -
     * @param int $from_uid 来自用户 ID
     * @param int $day 所属日期
     * @param int $from 来源
     * @param string $remark 备注
     * @param int $product_id 产品 ID
     * @param int $order_id 订单 ID
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function userLog(int $uid, int $wallet_type_id, $add = 0.00000, $from_uid = 0, $day = 0, $from = 0, $remark = '', $product_id = 0, $order_id = 0)
    {
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
        $old = $UserWalletService->checkbalance($uid, $wallet_type_id);

        // 注意此处可能会有精度损失 TODO
//        $new = number_fixed($old + $add);
        if ($add >= 0) {
            $new = bcadd($old, $add, 5);
        } elseif ($add < 0) {
            $new = bcsub($old, abs($add), 5);
        }

        $meta['old'] = $old;
        $meta['add'] = $add;
        $meta['new'] = $new;
        $meta['from'] = $from;
        $meta['product_id'] = $product_id;
        $meta['order_id'] = $order_id;
        $meta['remark'] = $remark;

        $UserWalletService->store($uid, $wallet_type_id, $add, $meta);// 写入数据到钱包

        UserWalletLog::create([
            'user_id' => $uid,
            'wallet_type_id' => $wallet_type_id,
            'from_user_id' => $from_uid,
            'day' => $day,
            'old' => $old,
            'add' => $add,
            'new' => $new,
            'from' => $from,
            'remark' => $remark,
            'product_id' => $product_id,
            'order_id' => $order_id,
        ]);
    }

    /**
     * @param int $wallet_type_id 钱包类型ID
     * @param  $team_a 分红池A
     * @param  $team_b 分红池B
     * @param  $team_c 分红池C
     * @param  $risk 风控账户
     * @param  $commission_balance 推荐
     * @param int $day 天数
     * @param int $from_uid 来自用户ID
     * @param string $remark 备注
     */
    public function SystemLog(int $wallet_type_id, $team_a = 0.00000, $team_b = 0.00000, $team_c = 0.00000, $risk = 0.00000, $commission_balance = 0.00000, $service_fee = 0.00000, $day = 0, $from_uid = 0, $remark = '', $product_id = 0, $order_id = 0)
    {
        if ($product_id > 0) {
            $wallet = SystemWallet::where('wallet_type_id', $wallet_type_id)
                ->where('product_id', $product_id)
                ->first();
        } else {
            $wallet = SystemWallet::where('wallet_type_id', $wallet_type_id)->first();
        }

        if (!$wallet) {
            if ($product_id > 0) {
                $product_id = $product_id;
            } else {
                $product = Product::where('wallet_type_id', $wallet_type_id)->first();
                $product_id = $product->id;
            }
            $wallet = SystemWallet::create([
                'product_id' => $product_id,
                'wallet_type_id' => $wallet_type_id,
            ]);
        }

//        $new_team_a = number_fixed($wallet->team_a + $team_a);
//        $new_team_b = number_fixed($wallet->team_b + $team_b);
//        $new_team_c = number_fixed($wallet->team_c + $team_c);
//        $new_risk = number_fixed($wallet->risk + $risk);
//        $new_commission_balance = number_fixed($wallet->commission_balance + $commission_balance);
//        $new_service_fee = number_fixed($wallet->service_fee + $service_fee);

        $new_team_a = bcadd($wallet->team_a, $team_a, 5);
        $new_team_b = bcadd($wallet->team_b, $team_b, 5);
        $new_team_c = bcadd($wallet->team_c, $team_c, 5);
        $new_risk = bcadd($wallet->risk, $risk, 5);
        $new_commission_balance = bcadd($wallet->commission_balance, $commission_balance, 5);
        $new_service_fee = bcadd($wallet->service_fee, $service_fee, 5);

        $log = SystemWalletLog::create([
            'system_wallet_id' => $wallet->id,
            'product_id' => $wallet->product_id,
            'wallet_type_id' => $wallet->wallet_type_id,
            'day' => $day,
            'old_team_a' => $wallet->team_a,
            'old_team_b' => $wallet->team_b,
            'old_team_c' => $wallet->team_c,
            'old_risk' => $wallet->risk,
            'old_commission_balance' => $wallet->commission_balance,
            'old_service_fee' => $wallet->service_fee,

            'team_a_add' => $team_a,
            'team_b_add' => $team_b,
            'team_c_add' => $team_c,
            'risk_add' => $risk,
            'commission_balance_add' => $commission_balance,
            'service_fee_add' => $service_fee,

            'team_a' => $new_team_a,
            'team_b' => $new_team_b,
            'team_c' => $new_team_c,
            'risk' => $new_risk,
            'commission_balance' => $new_commission_balance,
            'service_fee' => $new_service_fee,

            'from_user_id' => $from_uid,
            'remark' => $remark,
            'order_id' => $order_id,
        ]);

        $wallet->update([
            'team_a' => $new_team_a,
            'team_b' => $new_team_b,
            'team_c' => $new_team_c,
            'risk' => $new_risk,
            'commission_balance' => $new_commission_balance,
            'service_fee' => $new_service_fee,
        ]);
    }
}
