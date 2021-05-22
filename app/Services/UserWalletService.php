<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserWalletService
{
    // 操作写入钱包数据

    // 查询用户是否创建钱包
    public function checkWallet($uid)
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
}
