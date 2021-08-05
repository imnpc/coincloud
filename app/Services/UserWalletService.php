<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletType;
use Bavix\Wallet\Interfaces\Mathable;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Bavix\Wallet\Services\WalletService;
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

    // 获得用户昨日增加金额
    public function yesterday(int $uid, $wallet_type_id)
    {
        $user = User::find($uid);
        $this->checkWallet($uid); // 检测用户是否创建过钱包
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);

        $decimalPlaces = app(WalletService::class)->decimalPlaces($wallet);
        $decimalPlacesValue = app(WalletService::class)->decimalPlacesValue($wallet);

        // 昨日增加
        $data = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->where('wallet_id', '=', $wallet->id)
            ->whereDate('created_at', '=', Carbon::yesterday())
//            ->whereDate('created_at', '=', '2021-05-08')
            ->sum('amount');
        if ($data <= 0) {
            return 0;
        } else {
            return app(Mathable::class)->div($data, $decimalPlaces, $decimalPlacesValue);
        }
    }

    // 获取用户指定类型钱包累计收入
    public function total(int $uid, $wallet_type_id)
    {
        $user = User::find($uid);
        $this->checkWallet($uid); // 检测用户是否创建过钱包
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);

        $decimalPlaces = app(WalletService::class)->decimalPlaces($wallet);
        $decimalPlacesValue = app(WalletService::class)->decimalPlacesValue($wallet);

        $data = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->where('wallet_id', '=', $wallet->id)
            ->sum('amount');
        if ($data <= 0) {
            return 0;
        } else {
            return app(Mathable::class)->div($data, $decimalPlaces, $decimalPlacesValue);
        }
    }

    // 获取指定类型钱包当前总余额
    public function walletBalance($wallet_type_id)
    {
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;

        $data = Wallet::where('slug', $name)
            ->sum('balance');
        $wallet = Wallet::where('slug', $name)->first();
        if ($wallet) {
            $decimalPlaces = app(WalletService::class)->decimalPlaces($wallet);
            $decimalPlacesValue = app(WalletService::class)->decimalPlacesValue($wallet);

            if ($data <= 0) {
                return 0;
            } else {
                return app(Mathable::class)->div($data, $decimalPlaces, $decimalPlacesValue);
            }
        } else {
            return 0;
        }
    }

    // 获取指定类型钱包累计收入
    public function walletTotal($wallet_type_id)
    {
        $wallet_type = WalletType::find($wallet_type_id);
        $name = $wallet_type->slug;

        $wallet = Wallet::where('slug', $name)->first();
        $decimalPlaces = app(WalletService::class)->decimalPlaces($wallet);
        $decimalPlacesValue = app(WalletService::class)->decimalPlacesValue($wallet);

        $list = Wallet::where('slug', $name)->get();
        $ids = [];

        foreach ($list as $key => $value) {
            $ids[] = $value['id'];
        }
        $ids = array_values($ids);

        $data = Transaction::where('type', '=', Transaction::TYPE_DEPOSIT)
            ->whereIn('wallet_id', $ids)
            ->sum('amount');
        if ($data <= 0) {
            return 0;
        } else {
            return app(Mathable::class)->div($data, $decimalPlaces, $decimalPlacesValue);
        }
    }
}
