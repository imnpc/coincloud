<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\DayBonus;
use App\Models\DayFreed;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use App\Models\Weekly;
use App\Models\WeeklyLog;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;
use App\Traits\PassportToken;

class IndexController extends Controller
{
    use PassportToken;

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
                $banner_list[] = Storage::disk(config('filesystems.default'))->url($ban);
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

    public function aboutus(Request $request)
    {
        $data = Article::where('article_category_id', '=', 7)->where('status', 1)->first();
        return $data;
    }

    public function test()
    {

        $csv_file = Storage::disk('public')->url('2022-01-10.csv');
        $list = read_csv($csv_file);

        $list = array_values($list);
        //print_r($list);
        foreach ($list as $key => $value) {
            //echo $value[0];
//           echo $value[0].'--'.$value[1].'***';
           $pwd = md5("JwAhf0gPmHIJIZbiPlca".$value[1]);
            $data[] = "INSERT INTO `ey_users` (`username`, `password`, `nickname`, `head_pic`, `level`, `reg_time`)
            VALUES ('$value[0]', '$pwd', '$value[0]', '/public/static/common/images/dfboy.png', '1',
                    '1634016801');\r\n";
        }

        Storage::disk('public')->put('users-2022-01-10.sql', $data);
        print_r($data);

        //-- 转存表中的数据 `ey_users`  JwAhf0gPmHIJIZbiPlca.密码
        //--
        //
        //INSERT INTO `ey_users` (`username`, `password`, `nickname`, `head_pic`, `level`, `reg_time`)
        //VALUES ('H00006', '5ee31bfd0985da3d0b98e54ddf9ffd74', 'H00006', '/public/static/common/images/dfboy.png', '1',
        //        '1634016801');
        exit();
        $coins = 100;
        $bonus_team_a = 0;
        $team_a = @bcmul($coins, $bonus_team_a / 100, 5); // 分红池A
        echo $team_a;
        exit();

        $user = User::find(1);
// return  response
        return $this->getBearerTokenByUser($user, 1, true);
        exit();

        $bb = config('filesystems.default');
        echo $bb;
        exit();
        $list = WalletType::all();

        $options = [];
        foreach ($list as $key => $value) {
            $options[$value->id] = $value->slug;
        }
        print_r($options);
        exit();
        // 获得上月信息
        $lastmonth = Carbon::now()->subMonth();
        $year = $lastmonth->year;
        $month = $lastmonth->month;
        $begin_time = $lastmonth->startOfMonth()->toDateTimeString(); // 上月开始时间
        $end_time = $lastmonth->endOfMonth()->toDateTimeString(); // 上月结束时间
        echo $month . '*' . $year . '*' . $begin_time . '*' . $end_time;
        exit();
        //
        //$res = Good::where('deleted_at', null)
        //            ->selectRaw('count(good_id) as count_good, good_id')
        //            ->groupBy('good_id')
        //            ->with(['good' => function ($query) {
        //                return $query->select('id', 'goods_name');
        //            }])
        //            ->orderBy('count_good', 'desc')
        //            ->get()
        //            ->toArray();
        //
        //   dd($res);
        $lists = Order::where('deleted_at', null)
            ->selectRaw('product_id,sum(number) as num')
            ->where('status', '=', 0)
            ->where('pay_status', '=', 0)
            ->groupBy('product_id')
            ->pluck('num', 'product_id');
//           ->get();
        //->pluck('product_id', 'num');
        print_r($lists);
        foreach ($lists as $k => $v) {
            echo '产品ID-' . $k . '->数量：' . $v;
        }
        exit();
        // 3 4 5
        $wallet_type_id = 5;
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
        echo $UserWalletService->walletTotal($wallet_type_id);
        exit();
        $orders = Order::where('pay_status', '=', Order::PAID_COMPLETE)
            ->get(); // 支付状态 0-已完成
        foreach ($orders as $k => $v) {
            $check_pledge = Pledge::where('user_id', '=', $v->user_id)
                ->where('product_id', '=', $v->product_id)
                ->where('order_id', '=', $v->id)
                ->first();
            if (!$check_pledge) {
                echo $v->product->pledge_fee;
                exit();
            }
        }
//        $id = 1;
//        $ids = User::unlimitedCollectionById($id);
////        $users_son1 = [];
////
////        foreach ($ids as $key => $value) {
////            $users_son1[] = $value['id'];
////        }
////        $users1 = array_values($users_son1);
//        print_r($ids);

        exit();
    }

    public function demo()
    {
        $a = 9999;
        echo number_fixed($a);
        exit();
        $licensekey = config('app.license_key');
        $exists = Storage::disk('local')->exists('localkey.txt');
        if (!$exists) {
            $results = shy_check_license($licensekey);
        } else {
            $localkey = Storage::disk('local')->get('localkey.txt');
            $results = shy_check_license($licensekey, $localkey);
        }
//        print_r($results);
        // Interpret response
        switch ($results['status']) {
            case "Active":
                // get new local key and save it somewhere
                if(isset($results['localkey'])) {
                    $localkeydata = $results['localkey'];
                    Storage::disk('local')->put('localkey.txt', $localkeydata);
                    die("Online License key is OK");
                }
                die("Local License key is OK");
                break;
            case "Invalid":
                die("License key is Invalid");
                break;
            case "Expired":
                die("License key is Expired");
                break;
            case "Suspended":
                die("License key is Suspended");
                break;
            default:
                die("Invalid Response");
                break;
        }
    }
}
