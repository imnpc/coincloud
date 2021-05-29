<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserWalletService
{
    // 操作写入钱包数据
    public function store($uid, $wallet_type_id, $money, $remark)
    {
        $decimal = 0;
        $user = User::find($uid);
        $this->checkWallet($uid); // 检测用户是否创建过钱包
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);

        // 如果钱包带小数点
        if ($wallet->decimal_places > 0) {
            $decimal = 1;
        }

        if ($money > 0 && $decimal == 1) {
            $wallet->depositFloat($money, $remark); // 增加
        } elseif ($money > 0 && $decimal == 0) {
            $wallet->deposit($money, $remark); // 增加
        } elseif ($money < 0 && $decimal == 1) {
            $wallet->withdrawFloat($money, $remark); // 减少
        } elseif ($money < 0 && $decimal == 0) {
            $wallet->withdraw($money, $remark); // 减少
        }
    }

    // 查询用户是否创建钱包
    public function checkWallet(int $uid)
    {
        $user = User::find($uid);
//        $lists = WalletType::all(); // 钱包类型列表
        $lists = WalletType::where('is_enblened', '=', 1)->get(); // 钱包类型列表 启用 的
        foreach ($lists as $key => $value) {
            $check = $user->hasWallet($value->slug); // bool(false)
            if (!$check) {
                $wallet = $user->createWallet([
                    'name' => $value->name,
                    'slug' => $value->slug,
                    'description' => '用户 ' . $user->id . ' 的 ' . $value->description,
                    'decimal_places' => $value->decimal_places, // 钱包小数点
                ]);
            }
        }
    }

    // 获得用户指定钱包余额
    public function checkbalance(int $uid, $wallet_type_id)
    {
        $user = User::find($uid);
        $this->checkWallet($uid); // 检测用户是否创建过钱包
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);
        // 如果钱包带小数点
        if ($wallet->decimal_places > 0) {
            $decimal = 1;
        }
        if ($decimal == 1) {
            return $wallet->balanceFloat;
        } else {
            return $wallet->balance;
        }
    }

}
