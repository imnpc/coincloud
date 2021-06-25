<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\Product;
use App\Models\UserWalletLog;
use App\Services\UserWalletService;
use Bavix\Wallet\Models\Transaction;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets;
use Encore\Admin\Widgets\InfoBox;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('桌面')
            //->description('Description...')
//            ->row(Dashboard::title())
            ->row(function (Row $row) {
                $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
                $user_count = User::count();
                $order_count = Order::all()->count();
                $user_verify = User::where('is_verify',1)->count();
                $order_complete = Order::where('pay_status', '=', Order::PAID_COMPLETE)->count();

                $row->column(3, new Widgets\InfoBox('订单总数', 'shopping-cart', 'aqua', '', $order_count));
                $row->column(3, new Widgets\InfoBox('已审核订单', 'shopping-cart', 'aqua', '', $order_complete));
                $row->column(3, new Widgets\InfoBox('用户总数', 'users', 'aqua', '', $user_count));
                $row->column(3, new Widgets\InfoBox('实名用户', 'users', 'aqua', '', $user_verify));

                $product = Product::all();
                // 产品数据：累计产币量 累计可提币  未释放 未提币 有效T数 购买T数 质押币 GAS费
                foreach ($product as $k => $v) {
                    $total = UserBonus::where('product_id', $v->id)
                        ->sum('coins'); // 总的累计产币量
                    $total_revenue = UserBonus::where('product_id', $v->id)
                        ->sum('coin_day'); // 累计可提币
                    $wait_coin = Freed::where('product_id', $v->id)
                        ->sum('wait_coin'); // 未释放
                    $coin_balance = $UserWalletService->walletBalance($v->wallet_type_id);// 未提币
                    $valid_power = Order::where('product_id', '=', $v->id)
                        ->where('pay_status', '=', Order::PAID_COMPLETE)
                        ->sum('valid_power'); // 有效 T 数
                    $buy = Order::where('product_id', '=', $v->id)
                        ->where('pay_status', '=', Order::PAID_COMPLETE)
                        ->sum('number'); // 购买 T 数
                    $pledge_fee = Pledge::where('product_id', '=', $v->id)
                        ->sum('pledge_coins'); // 质押币
                    $gas_fee = Pledge::where('product_id', '=', $v->id)
                        ->sum('gas_coins'); // GAS 费

                    $row->column(3, new Widgets\InfoBox($v->name.'- 累计产币量', '', 'red', '', $total));
                    $row->column(3, new Widgets\InfoBox('累计可提币量', '', 'red', '', $total_revenue));
                    $row->column(3, new Widgets\InfoBox('未释放', '', 'red', '', $wait_coin));
                    $row->column(3, new Widgets\InfoBox('未提币', '', 'red', '', $coin_balance));

                    $row->column(3, new Widgets\InfoBox($v->name.'- 有效 T 数', '', 'yellow', '', $valid_power));
                    $row->column(3, new Widgets\InfoBox('购买 T 数', '', 'yellow', '', $buy));
                    $row->column(3, new Widgets\InfoBox('质押币', '', 'yellow', '', $pledge_fee));
                    $row->column(3, new Widgets\InfoBox('GAS 费', '', 'yellow', '', $gas_fee));
                }

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
