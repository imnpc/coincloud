<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RechargeAccountLogResource;
use App\Http\Resources\RechargeResource;
use App\Models\DayBonus;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\Recharge;
use App\Models\RechargeAccountLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Storage;
use Spatie\QueryBuilder\QueryBuilder;

class RechargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $logs = QueryBuilder::for(Recharge::class)
            ->allowedFilters([
                AllowedFilter::exact('wallet_type_id'), // 钱包类型 ID
            ])
            ->defaultSort('-created_at')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'order_sn', 'wallet_type_id', 'pay_type', 'coin', 'reason', 'pay_status', 'created_at')
            ->paginate();

        foreach ($logs as $k => $v) {
            $logs[$k]['can_cancel'] = 0;
            $logs[$k]['add'] = "+" . $v['coin'];
            $logs[$k]['remark'] = "充值 " . $v['coin'];
            if (($v['pay_type'] == 2 && $v['pay_status'] == 2) || ($v['pay_type'] == 1 && $v['pay_status'] > 0)) {
                $logs[$k]['can_cancel'] = 1;// 是否可以取消充值
            }
        }

        return RechargeResource::collection($logs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $image = image_ext(); // 上传图片类型

        $request->validate([
            'coin' => 'required|numeric|not_in:0|min:1', // 充值金额
            'pay_type' => 'required|numeric|in:1,2', // 支付类型 1-充值 2-账户转入
            'pay_image' => 'required_if:type,1|mimes:' . $image, // 如果 type=1 支付凭证图片必须有
            'wallet_type_id' => 'required|exists:wallet_types,id', // 钱包类型
        ]);

        $day = Carbon::yesterday()->toDateString();// 获得日期

        if ($request->pay_type == 1) {
            $upload = upload_images($request->file('pay_image'), 'recharge', $user->id);
            $pay_image = $upload->path;
            $confirm_time = NULL;
            $pay_status = 1;
        }
        if ($request->pay_type == 2) {
            $pay_image = NULL;
            $confirm_time = Carbon::now();
            $pay_status = 2;
        }

        $product = Product::where('wallet_type_id', $request->wallet_type_id)->first();
        $pledge_fee = $product->pledge_fee; // 当天质押币系数
        $gas_fee = $product->gas_fee; // 当天单T有效算力封装成本

        $num = Order::where('user_id', '=', $user->id)
            ->where('product_id', $product->id)
            ->where('pay_status', 0)
            ->where('status', 0)
            ->sum('number'); // 有效算力 TODO
        $has_pledge = Pledge::where('user_id', '=', $user->id)
            ->where('product_id', $product->id)
            ->sum('power'); // 已经封装质押币的算力
        $can_pledge = $num - $has_pledge;
        if ($can_pledge <= 0) {
            $data['message'] = "暂时没有需要封装的数量";
            return response()->json($data, 403);
        }

        $snowflake = app('Kra8\Snowflake\Snowflake');
        $order_sn = $snowflake->next();// 生成订单号 雪花算法

        $order = Recharge::create([
            'order_sn' => $order_sn,
            'user_id' => $user->id,
            'wallet_type_id' => $request->wallet_type_id,
            'coin' => $request->coin,
            'pay_type' => $request->pay_type,
            'pay_time' => Carbon::now(),
            'pay_image' => $pay_image,
            'confirm_time' => $confirm_time,
            'pay_status' => $pay_status,
            'pledge' => $product->pledge_fee,
            'gas_fee' => $product->gas_fee,
        ]);

        $data['message'] = "支付凭证已提交,请等待审核";
        return response()->json($data, 200);
    }

    /**
     * 我的充值
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function my(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'wallet_type_id' => 'required|exists:wallet_types,id', // 钱包类型
        ]);

        $product = Product::where('wallet_type_id', $request->wallet_type_id)->first();

        $wallet_address = $product->coin_wallet_address; // 钱包地址
        $wallet_qrcode = Storage::disk('oss')->url($product->coin_wallet_qrcode); // 钱包二维码

        $day = Carbon::yesterday()->toDateString();// 获得日期

        $pledge_fee = $product->pledge_fee; // 当天质押币系数
        $gas_fee = $product->gas_fee; // 当天单T有效算力封装成本
        $each_fee = number_fixed($pledge_fee + $gas_fee); // 封装满单T所需FIL币成本

        $num = Order::where('user_id', '=', $user->id)
            ->where('product_id', $product->id)
            ->where('pay_status', 0)
            ->where('status', 0)
            ->sum('number'); // 有效算力 TODO
        $has_pledge = Pledge::where('user_id', '=', $user->id)
            ->where('product_id', $product->id)
            ->sum('power'); // 已经封装质押币的数量
        $can_pledge = $num - $has_pledge;
        if ($can_pledge <= 0) {
            $data['message'] = "暂时没有需要封装的数量";
            return response()->json($data, 403);
        }

//        $other_coin = Recharge::where('user_id', '=', $user->id)
//            ->where('schedule', '=', 0)
//            ->where('wallet_type_id', '=', $request->wallet_type_id)
//            ->sum('coin'); // 其他总排单金额
//        $used = Recharge::where('user_id', '=', $user->id)
//            ->where('schedule', '=', 0)
//            ->where('wallet_type_id', '=', $request->wallet_type_id)
//            ->sum('used_coin'); // 其他已使用排单金额
//        $other_recharge = number_fixed($other_coin - $used);
//
//        $wait_power = Order::where('user_id', '=', $user->id)
//            ->where('product_id', $product->id)
//            ->where('pay_status', 0)
//            ->where('status', 0)
//            ->sum('valid_power'); // 有效算力 TODO

        $max = number_fixed($can_pledge * $each_fee);

        $pledge_days = $product->pledge_days; // 天数
        //$day_limit = config('recharge.day_limit'); // 每天可封装T数
        $data = [
            'wallet_address' => $wallet_address,
            'wallet_qrcode' => $wallet_qrcode,
            'pledge' => $pledge_fee,
            'gas' => $gas_fee,
            'each_fee' => $each_fee,
            'wait_power' => $can_pledge,
            'max' => $max,
            'pledge_days' => $pledge_days,
            'list_num' => 0,
            'wallet_slug' => $product->wallet_slug,
            //'day_limit' => $day_limit,
        ];

        return $data;
    }

    /**
     * 算力封装记录
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function powerlog(Request $request)
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $logs = QueryBuilder::for(RechargeAccountLog::class)
            ->defaultSort('-created_at')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'recharge_id', 'user_id', 'day', 'power', 'pledge', 'gas', 'total', 'wallet_type_id')
            ->paginate();

        foreach ($logs as $k => $v) {
            $logs[$k]['add'] = "+" . $v['coin'];
            $logs[$k]['remark'] = "增加算力 " . $v['power'] . ',扣除 ' . $v['total'] . ' ' . $v['wallet_slug'];
        }

        return RechargeAccountLogResource::collection($logs);
    }
}
