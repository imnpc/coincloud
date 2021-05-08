<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //获取当前用户
        $user = User::first();

        $check = $user->hasWallet('USDT'); // bool(false)
        if (!$check) {
            $wallet = $user->createWallet([
                'name' => 'USDT 钱包',
                'slug' => 'USDT',
                'description' => '用户' . $user->id . '的 USDT 钱包',
                'decimal_places' => '5', // 钱包小数点
            ]);
        } else {
            $wallet = $user->getWallet('USDT');
        }

        //$meta['remark'] = '测试增加理由,订单ID #1';
        //$wallet->deposit(10,$meta); //增加数量
        //echo $wallet->balance; // 显示数量(整数)
        //$wallet->depositFloat(0.96999); //增加数量(小数)
        //echo $wallet->transactions()->first()->amount;
        $lists = $wallet->transactions()->get();
        //print_r($lists->toArray());
//        foreach ($lists as $list) {
//            // echo $user->wallet->balance;
//            echo $list->amount; // Abbreviated notation
//            echo "<br>";
//        }
        //dd($wallet->balanceFloat); // 显示数量(小数)
        exit();
        //初始化钱包
        $user->balance;
        //充值 100
        $user->deposit(100);
        //打印看一下是个啥
        dd($user->balance); // 字符串类型的一百
        // 继续充两次看看数据库有些什么记录
//        $user->deposit(100);
//        $user->deposit(100);
    }
}
