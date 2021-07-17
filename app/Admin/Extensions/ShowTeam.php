<?php
/**
 *  显示用户的团队 查询2级
 */

namespace App\Admin\Extensions;

use App\Models\Order;
use App\Models\Product;
use App\Models\UserBonus;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use App\Models\User;
use Encore\Admin\Widgets\Table;

class ShowTeam implements Renderable
{
    public function render($key = null)
    {
        $data = [];
        $data2 = [];
        $html = '';
        // 查询我的团队
        $parent = User::with('sons.sons')
            ->find($key)
            ->toArray();

        $users_son1 = [];
        $users_son2 = [];
        $total = []; // 统计数据
        $orders1_total = 0; // 直接-总T数
        $orders2_total = 0; // 间接-总T数
        $orders1_num = 0; // 直接-人数
        $orders2_num = 0; // 间接-人数
        $orders1_coin = 0; // 直接-产币
        $orders2_coin = 0; // 间接-产币

        $day = Carbon::now()->subDay()->toDateString();// 获得前一天日期 TODO

        // 循环团队-直接
        foreach ($parent['sons'] as $key => &$value) {
            $users_son1[] = $value['id'];
            // 循环团队-间接
            if ($value['sons']) {
                foreach ($value['sons'] as $k => &$v) {
                    $users_son2[] = $v['id'];
                    $v['yesterday_coin'] = 0; // 昨日新增可提币
                    $v['coin'] = 0; // 昨日产币
                    $bonus2 = UserBonus::where('day', '=', $day)
                        ->where('user_id', '=', $v['id'])
                        ->first();// 查询前一天系统分红记录
                    if ($bonus2) {
                        $v['yesterday_coin'] = $bonus2->coin_day;
                        $v['coin'] = $bonus2->coin;
                        $orders2_coin += $bonus2->coin;
                    }

                    $orders2_num += 1;
                    $order2_num = Order::where('user_id', '=', $v['id'])->count();
                    $url = '<a href="/admin/user-wallet-logs?user_id=' . $v['id'] . '" target="_blank">钱包日志</a>';
                    $order_url = '<a href="/admin/orders?user_id=' . $v['id'] . '" target="_blank">订单列表<span class="label label-danger">' . $order2_num . '</span></a>';
                    $data2[] = [$v['id'], $v['nickname'] . '<span class="label label-primary">' . $v['parent_id'] . '</span>', $v['mobile'], '<span class="label label-warning">间接</span>', $order_url, $url];
                }
            }
            $value['yesterday_coin'] = 0; // 昨日新增可提币
            $value['coin'] = 0; // 昨日产币
            $bonus1 = UserBonus::where('day', '=', $day)
                ->where('user_id', '=', $value['id'])
                ->first();// 查询前一天系统分红记录
            if ($bonus1) {
                $value['yesterday_coin'] = $bonus1->coin_day;
                $value['coin'] = $bonus1->coin;
                $orders1_coin += $bonus1->coin;
            }

            $orders1_num += 1;
            $order_num = Order::where('user_id', '=', $value['id'])->count();
            $url = '<a href="/admin/user-wallet-logs?user_id=' . $value['id'] . '" target="_blank">钱包日志</a>';
            $order_url = '<a href="/admin/orders?user_id=' . $value['id'] . '" target="_blank">订单列表<span class="label label-danger">' . $order_num . '</span></a>';
            $data[] = [$value['id'], $value['nickname'], $value['mobile'], '<span class="label label-success">直接</span>', $order_url, $url];
        }
        $users1 = array_values($users_son1);
        $users2 = array_values($users_son2);
        $product = Product::all();
        // 产品数据
        foreach ($product as $k => $v) {
            $orders1 = Order::where('product_id', '=', $v->id)
                ->where('pay_status', '=', Order::PAID_COMPLETE)
                ->whereIn('user_id', $users1)
                ->sum('number'); // 购买 T 数
            $orders2 = Order::where('product_id', '=', $v->id)
                ->where('pay_status', '=', Order::PAID_COMPLETE)
                ->whereIn('user_id', $users2)
                ->sum('number'); // 购买 T 数
            $product_data[] = [$v['name'], $orders1, $orders2];
        }

        $data = array_merge(array_values($data), array_values($data2)); // 合并 直接 间接 数据
        $total[] = ['', $orders1_num . ' 人', $orders2_num . ' 人'];
        $html .= new Table(['推荐人数统计', '直接-人数', '间接-人数'], $total);
        $html .= new Table(['产品名', '直接购买数量', '间接购买数量'], $product_data);
        $html .= new Table(['ID', '昵称--推荐人ID', '手机号', '推荐级别', '订单/数量', '查看明细'], $data);

        return <<<HTML
{$html}
HTML;
    }
}
