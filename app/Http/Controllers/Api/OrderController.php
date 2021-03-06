<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;
use Storage;

class OrderController extends Controller
{
    /**
     * 我的订单
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list
        $now = Carbon::now()->toDateTimeString();
        $lists = QueryBuilder::for(Order::class)
            ->defaultSort('-created_at')
            ->with('product')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'product_id', 'order_sn', 'number', 'pay_money', 'status', 'pay_status', 'payment', 'payment_type', 'confirm_time')
            ->paginate();
        $ext = '';
        foreach ($lists as $key => $value) {
            if ($value->payment == Order::PAY_BANK || $value->payment == Order::PAY_ADMIN) {
                $ext = '￥';
            } else {
                $ext = $value->payment_type;
            }

            $lists[$key]['pay_money'] = $value->pay_money . ' ' . $ext;
            $lists[$key]['product_name'] = $value->product['name'];
            if ($value->confirm_time) {
                $lists[$key]['after_days'] = $value->confirm_time->diffInDays($now); // 已经过去天数
            } else {
                $lists[$key]['after_days'] = 0;
            }


            unset($value->product);
        }

        return OrderResource::collection($lists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 提交订单
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 获取产品信息
        $product = Product::find($request->product_id);

        $request->validate([
            'product_id' => 'required|exists:products,id', // 产品 ID
            'number' => 'required|numeric|min:'.$product->min_buy, // 购买数量
            'payment' => 'required|numeric|in:1,2,3', // 支付方式 1-银行转账 2-USDT 3-其他虚拟币
        ]);

        $user = $request->user();

        if ($product->is_sold_out == 1) {
            $data['message'] = "产品已售罄";
            return response()->json($data, 403);
        }
	
        // 实名
//        if ($user->is_verify == 0 && $user->real_name) {
//            $data['message'] = "认证审核中";
//            return response()->json($data, 403);
//        } else if ($user->is_verify == 0 && empty($user->real_name)) {
//            $data['message'] = "请先到会员中心进行实名认证！";
//            return response()->json($data, 403);
//        }

        // 获取产品单价
        if ($request->payment == Order::PAY_BANK) {
            // 人民币支付
            $unit_price = $product->price; // 单价
            $type = 'RMB'; // 单位
            $payment = [
                'card_number' => config('order.card_number'), // 银行卡号
                'card_name' => config('order.card_name'), // 开户人姓名
                'account_with_bank' => config('order.account_with_bank'), // 开户行
            ];
        } else if ($request->payment == Order::PAY_USDT) {
            // USDT 支付
            $unit_price = $product->price_usdt; // 单价
            $type = 'USDT'; // 单位
            $payment = [
                'wallet_address' => config('order.wallet_usdt_address'), // USDT 钱包地址
                'wallet_qrcode' => Storage::disk(config('filesystems.default'))->url(config('order.wallet_usdt_qrcode')), // USDT 钱包二维码
            ];
        } else if ($request->payment == Order::PAY_COIN) {
            // 其他虚拟币支付
            $unit_price = $product->price_coin; // 单价
            $type = $product->wallet_slug; // 虚拟币单位
            $payment = [
                'wallet_address' => $product->coin_wallet_address, // 虚拟币 钱包地址
                'wallet_qrcode' => Storage::disk(config('filesystems.default'))->url($product->coin_wallet_qrcode), // 虚拟币 钱包二维码
            ];
        }

//        $price = $unit_price * $request->number; // 订单价格
        $price = @bcmul($unit_price, $request->number, 2); // 订单价格

        if ($price < 1) {
            $data['message'] = "订单金额数据错误";
            return response()->json($data, 403);
        }

        $snowflake = app('Kra8\Snowflake\Snowflake');
        $order_sn = $snowflake->next();// 生成订单号 雪花算法
        // 如果等待天数大于0
        $wait_status = 0;
        if ($product->wait_days > 0) {
            $wait_status = 1; // 等待状态 0-已生效 1-等待中
        }
        // 如果检测需要封装
        if ($product->package_rate > 0) {
            $valid_power = 0;
            $package_status = 1; // 封装状态 0-封装完成 1-等待封装 2-封装中
        } else {
//            $valid_power = @number_fixed(($request->number * $product->valid_rate) / 100, 2); // 有效T数 = 购买数量 * 有效T数比例
            $valid_power = @bcmul($request->number, $product->valid_rate / 100, 2); // 有效T数 = 购买数量 * 有效T数比例
            $package_status = 0;
        }
        if ($product->revenue_type == 1) {
            $revenue_status = 1;
        } else {
            $revenue_status = 0;
        }
        // 提交订单
        $order = Order::create([
            'order_sn' => $order_sn, // 订单编号
            'user_id' => auth('api')->id(), // 所属用户 ID
            'product_id' => $request->product_id, // 产品 ID
            'wallet_type_id' => $product->wallet_type_id, // 支付方式钱包类型 ID
            'number' => $request->number, // 购买数量
            'pay_money' => $price, // 支付金额
            'wait_days' => $product->wait_days, // 等待天数
            'wait_status' => $wait_status, // 等待状态 0-已生效 1-等待中
            'valid_days' => $product->valid_days, // 有效天数
            'valid_rate' => $product->valid_rate, // 有效T数比例
            'valid_power' => $valid_power, // 当前有效T数
//            'max_valid_power' => @number_fixed(($request->number * $product->valid_rate) / 100, 2), // 最大有效T数
            'max_valid_power' => @bcmul($request->number, $product->valid_rate / 100, 2), // 最大有效T数
            'package_rate' => $product->package_rate, // 封装比例
            'package_already' => 0, // 已封装数量
            'package_wait' => $valid_power, // 等待封装数量
            'package_status' => $package_status, // 封装状态 0-封装完成 1-等待封装 2-封装中
            'payment' => $request->payment, // 支付方式 0-后台 1-银行转账 2-USDT 3-其他虚拟币
            'payment_type' => $type, // 付款类型
            'pay_status' => 1, // 支付状态 0-已完成 1-未提交 2-审核中
//            'is_output_coin' => $product->is_output_coin, // 是否产币 0-是 1-否 TODO
            'status' => 0, // 订单状态 0-有效 1-无效
            'revenue_type' => $product->revenue_type, // 收益类型 0-默认  1-pledge 收满质押币
            'revenue_status' => $revenue_status, // 收益状态 0-默认无需执行  1-执行中
        ]);

        // 提交订单以后获取支付方式详细信息 银行账号 或者 钱包地址 二维码
        $data = [];
        $data['order_id'] = $order->id;
        $data['total_price'] = $price;
        $data['type'] = $type;
        $data['number'] = $request->number;
        $data['payment'] = $payment;

        return $data;

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
        $this->authorize('own', $order);
        return new OrderResource($order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('own', $order);
        $user = $request->user();

        // 支付状态 0-已完成 1-未提交 2-审核中
        if ($order->pay_status == 0) {
            $data['message'] = "支付已完成";
            return response()->json($data, 403);
        }

        $upload = upload_images($request->file('pay_image'), 'payorder', $user->id);

        $order->update([
            'pay_image' => $upload->path,
            'pay_status' => 2,
            'pay_time' => Carbon::now(),
        ]);

        $data['message'] = "支付凭证已提交";
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * 预览检测订单
     * @param Request $request
     * @return array
     */
    public function check(Request $request)
    {
        // 获取产品信息
        $product = Product::find($request->product_id);

        $request->validate([
            'product_id' => 'required|exists:products,id', // 产品 ID
            'number' => 'required|numeric|min:'.$product->min_buy, // 购买数量
            'payment' => 'required|numeric|in:1,2,3', // 支付方式 1-银行转账 2-USDT 3-其他虚拟币
        ]);

        if ($product->is_sold_out == 1) {
            $data['message'] = "产品已售罄";
            return response()->json($data, 403);
        }

        // 获取产品单价
        if ($request->payment == Order::PAY_BANK) {
            // 人民币支付
            $unit_price = $product->price; // 单价
            $type = 'RMB'; // 单位
            $payment = [
                'card_number' => config('order.card_number'), // 银行卡号
                'card_name' => config('order.card_name'), // 开户人姓名
                'account_with_bank' => config('order.account_with_bank'), // 开户行
            ];
        } else if ($request->payment == Order::PAY_USDT) {
            // USDT 支付
            $unit_price = $product->price_usdt; // 单价
            $type = 'USDT'; // 单位
            $payment = [
                'wallet_address' => config('order.wallet_usdt_address'), // USDT 钱包地址
                'wallet_qrcode' => Storage::disk(config('filesystems.default'))->url(config('order.wallet_usdt_qrcode')), // USDT 钱包二维码
            ];
        } else if ($request->payment == Order::PAY_COIN) {
            // 其他虚拟币支付
            $unit_price = $product->price_coin; // 单价
            $type = $product->wallet_slug; // 虚拟币单位
            $payment = [
                'wallet_address' => $product->coin_wallet_address, // 虚拟币 钱包地址
                'wallet_qrcode' => Storage::disk(config('filesystems.default'))->url($product->coin_wallet_qrcode), // 虚拟币 钱包二维码
            ];
        }

//        $price = $unit_price * $request->number; // 订单价格
//        $pledge_fee = $product->pledge_fee * $request->number; // 质押币

        $price = @bcmul($unit_price, $request->number, 2); // 订单价格
        $pledge_fee = @bcmul($product->pledge_fee, $request->number, 2); // 质押币

        if ($price < 1) {
            $data['message'] = "订单金额数据错误";
            return response()->json($data, 403);
        }

        $data = [];
        $data['total_price'] = $price;
        $data['pledge_fee'] = $pledge_fee;
        $data['type'] = $type;
        $data['number'] = $request->number;
        $data['payment'] = $payment;

        return $data;
    }

    /**
     * 获取产品价格列表
     * @param Request $request
     * @return array
     */
    public function getprice(Request $request)
    {
        // 获取产品信息
        $product = Product::find($request->product_id);

        $request->validate([
            'product_id' => 'required|exists:products,id', // 产品 ID
            'number' => 'required|numeric|min:'.$product->min_buy, // 购买数量
        ]);

        if ($product->is_sold_out == 1) {
            $data['message'] = "产品已售罄";
            return response()->json($data, 403);
        }

        $data['price'] = @bcmul($product->price, $request->number, 2);
        $data['price_usdt'] = @bcmul($product->price_usdt, $request->number, 2);
        $data['price_coin'] = @bcmul($product->price_coin, $request->number, 2);
        $data['pledge_fee'] = @bcmul($product->pledge_fee, $request->number, 2);
        $data['number'] = $request->number;
        $data['product_id'] = $request->product_id;
        $data['price_coin_type'] = $product->wallet_slug;

        return $data;
    }
}
