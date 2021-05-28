<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ArticleCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Storage;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $data = [];
        // Banner 图片
        $banner = [];
        $banner[] = config('app.banner1');
        $banner[] = config('app.banner2');
        $banner[] = config('app.banner3');
        $banner_list = [];
        foreach ($banner as $ban) {
            if ($ban) {
                $banner_list[] = Storage::disk('oss')->url($ban);
            } else {
                return '';
            }
        }
        $data['banner'] = array_values($banner_list);

        // 获取公告
        $announcement = Announcement::where('is_recommand', '=', 1)
            ->where('status', '=', 1)
            ->select('id', 'title')
            ->get();
        $data['announcement'] = $announcement;

        // 导航栏
        $nav = ArticleCategory::where('status', '=', 1)
            ->orderBy('order', 'asc')
            ->select('id', 'title', 'icon')
            ->get();
        $data['nav'] = $nav;

        // 矿池运营数据 TODO
        $list = Product::where('status', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        //print_r($list->toArray());
        foreach ($list as $k => $v) {
            //
            $data['coinlist'][$k]['name'] = $v->wallet_slug;
            $data['coinlist'][$k]['total_revenue'] = $v->total_revenue;
            $data['coinlist'][$k]['yesterday_revenue'] = $v->yesterday_revenue;
            $data['coinlist'][$k]['yesterday_gas'] = $v->yesterday_gas;
            $data['coinlist'][$k]['yesterday_efficiency'] = $v->yesterday_efficiency;
            $data['coinlist'][$k]['total_revenue_text'] = $v->total_revenue_text;
            $data['coinlist'][$k]['yesterday_revenue_text'] = $v->yesterday_revenue_text;
            $data['coinlist'][$k]['yesterday_gas_text'] = $v->yesterday_gas_text;
            $data['coinlist'][$k]['yesterday_efficiency_text'] = $v->yesterday_efficiency_text;
        }

        // 矿池总产量 total_revenue 昨日产量 yesterday_revenue  昨日消耗GAS yesterday_gas 挖矿效率 yesterday_efficiency
//        $data['filpool']['progress'] = config('filpool.progress'); // 矿池填充进度
//        $data['filpool']['node_power'] = config('filpool.node_power'); // 节点总有效算力
//        $data['filpool']['community_power'] = config('filpool.community_power'); // 社区总有效算力
//        $data['filpool']['community_torage_space'] = config('filpool.community_torage_space'); // 社区总存储空间
//        $data['filpool']['total_revenue'] = config('filpool.total_revenue'); // 矿池总收益
//        $data['filpool']['yesterday_revenue'] = config('filpool.yesterday_revenue'); // 昨日收益
//        $data['filpool']['yesterday_gas'] = config('filpool.yesterday_gas'); // 昨日消耗GAS
//        $data['filpool']['single_revenue'] = config('filpool.single_revenue'); // 有效算力单T收益

        // 产品列表
        $product = Product::where('status', '=', 0)
            ->orderBy('id', 'desc')
            ->select('id', 'name', 'thumb', 'choose_reason')
            ->get();

        $data['product'] = $product;

        return $data;
    }
}
