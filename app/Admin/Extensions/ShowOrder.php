<?php
/**
 *  显示用户的订单 限制 10条
 */

namespace App\Admin\Extensions;

use Illuminate\Contracts\Support\Renderable;
use App\Models\Order;
use App\Models\Product;
use Encore\Admin\Widgets\Table;

class ShowOrder implements Renderable
{
    public function render($key = null)
    {
        $data = [];
        $order = Order::where('user_id', '=', $key)
//            ->limit(10)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
        if ($order) {
            foreach ($order as $k => $v) {
                $product = Product::find($v['product_id']);
                $product_name = $product ? $product->name : "";
                $data[$k] = [$v['id'], $v['order_sn'], $v['number'], $v['created_at'], $v['paid_text'], $product_name];
            }
        }

        $html = new Table(['ID', '订单号', '购买数量', '购买时间', '状态', '产品名'], $data);
        return <<<HTML
{$html}
HTML;
    }
}
