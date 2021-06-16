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
                //Product
                // 统计数据 调用模型
                //1、总的累计产币量。
                //2、总的累计可提币量。
                //3、总的未释放币量。
                //4、总的未提币量。
                //5、总的购买T数（1	、2号产品单独显示）。
                //6、总的有效T数（1	、2号产品单独显示）
                //7、总的质押币数量。
                //8、总的GAS费数量
                //9、总的奖励T数
                //10、总的出借数量。
                //11、总的累计利息
                //12.线性释放额外扣除的
                $product = Product::all();
                // 产品数据：累计产币量 累计可提币  未释放 未提币 有效T数 购买T数 质押币 GAS费
                foreach ($product as $k => $v) {
                    $total = UserBonus::where('product_id', $v->id)
                        ->sum('coins'); // 总的累计产币量
                    $total_revenue = UserBonus::where('product_id', $v->id)
                        ->sum('coin_day'); // 累计可提币
                    $wait_coin = Freed::where('product_id', $v->id)
                        ->sum('wait_coin'); // 未释放
                    // 未提币 TODO

                    $valid_power = Order::where('product_id', '=', $v->id)
                        ->where('pay_status', '=', Order::PAID_COMPLETE)
                        ->sum('valid_power'); // 有效 T 数
                    $buy = Order::where('product_id', '=', $v->id)
                        ->where('pay_status', '=', Order::PAID_COMPLETE)
                        ->sum('number'); // 购买 T 数
                    $pledge_fee = Pledge::where('product_id', '=', $v->id)
                        ->sum('coins'); // 质押币
                    $gas_fee = Pledge::where('product_id', '=', $v->id)
                        ->sum('gas_coins'); // GAS 费
                }

                $filecoin_total = UserBonus::all()->sum('coin'); // 总的累计产币量
                $coin_total = UserWalletLog::where('add', '>', '0')
                    ->sum('add'); // 总的累计可提币量
                $wait_coin = Freed::all()->sum('wait_coin'); // 总的未释放币量
                $coin_now = User::all()->sum('filecoin_balance'); // 总的未提币量
                $one_buy = Order::where('product_id', '=', 3)
                    ->where('pay_status', '=', Order::PAID_COMPLETE)
                    ->sum('number'); // 总的购买 T 数 1号
                $one_vaild = Order::where('product_id', '=', 3)
                    ->where('pay_status', '=', Order::PAID_COMPLETE)
                    ->where('wait_status', '=', 0)
                    ->sum('max_valid_power'); // 总的有效 T 数 1 号

                $reward = User::all()->sum('cloud_power_reward'); // 总的奖励 T 数

                $other_fee = Freed::all()->sum('other_fee'); // 线性释放额外扣除的

                $row->column(3, new Widgets\InfoBox('订单总数', 'shopping-cart', 'aqua', '', $order_count));
                $row->column(3, new Widgets\InfoBox('用户总数', 'users', 'aqua', '', $user_count));
                $row->column(3, new Widgets\InfoBox('累计产币量', '', 'aqua', '', $filecoin_total));
                $row->column(3, new Widgets\InfoBox('累计可提币量', '', 'aqua', '', $coin_total));

                $row->column(3, new Widgets\InfoBox('未释放币量', '', 'green', '', $wait_coin));
                $row->column(3, new Widgets\InfoBox('未提币量', '', 'green', '', $coin_now));


                $row->column(3, new Widgets\InfoBox('购买 T 数(1号)', '', 'yellow', '', $one_buy));
                $row->column(3, new Widgets\InfoBox('有效 T 数(1号)', '', 'yellow', '', $one_vaild));

                $row->column(3, new Widgets\InfoBox('总的奖励 T 数', '', 'red', '', $reward));

                $row->column(3, new Widgets\InfoBox('额外扣除', '', 'red', '', $other_fee));


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
