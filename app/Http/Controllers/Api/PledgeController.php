<?php

/**
 *  占用 需要开发调整 TODO
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PledgeResource;
use App\Models\Pledge;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $lists = QueryBuilder::for(Pledge::class)
            ->allowedFilters([
                AllowedFilter::exact('wallet_type_id'), // 钱包类型 ID
            ])
            ->defaultSort('-created_at')
            ->where('user_id', '=', auth('api')->id())
            ->paginate();

        foreach ($lists as $k => $v) {
            $lists[$k]['add'] = "+" . $v['pledge_coins'];
            $lists[$k]['remark'] = "质押数量 " . $v['pledge_coins'];
        }

        return PledgeResource::collection($lists);
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
     * 提取质押币
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //wallet_type_id
        $day = Carbon::now()->toDateString();// 获得前一天日期
        $image = image_ext(); // 上传图片类型
        $min = config('withdraw.min');
        $user = $request->user();

        // 需要实名认证 TODO
        if ($user->is_verify == 0) {
            $data['message'] = "请先到会员中心进行实名认证！";
            return response()->json($data, 403);
        }

        if (empty($user->money_password)) {
            $data['message'] = "请先到会员中心设置资金密码！";
            return response()->json($data, 403);
        }

        if (!Hash::check($request->money_password, $user->money_password)) {
            $data['message'] = "资金密码错误! ";
            return response()->json($data, 404);
        }

        // wallet_type_id 钱包类型/slug/获取对应钱包 TODO
        $wallet_type = WalletType::find($request->wallet_type_id);
        if (empty($wallet_type) || $wallet_type->is_enblened = 0) {
            $data['message'] = "不支持该类型！";
            return response()->json($data, 403);
        }
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);
        $balance = $wallet->balanceFloat;

        if ($balance <= 0) {
            $data['message'] = "无可提币金额!";
            return response()->json($data, 404);
        }

        if ($balance < $min) {
            $data['message'] = "当前账户" . $name . "币少于最小提币金额!";
            return response()->json($data, 404);
        }

        $request->validate([
            'image' => 'required|mimes:' . $image, // 缩略图
            'wallet_address' => 'required|string', // 钱包地址
            'money_password' => 'required|string', // 资金密码
            'wallet_type_id' => 'required|exists:wallet_types,id', // 钱包类型
            'coin' => 'required|numeric|not_in:0|min:' . $min . '|max:' . $balance, // 提币金额
        ]);

        $file = $request->file('image');

        $upload = upload_images($request->file('image'), 'withdraw', $user->id);

        if ($request->coin > $balance) {
            $data['message'] = "提币金额不能超过账户余额!";
            return response()->json($data, 404);
        }

        $fee = config('withdraw.coin_fee'); // 手续费
        $real_coin = number_fixed($request->coin - $fee); // 实际到账金额 = 申请提币金额 - 手续费
        $withdraw = Withdraw::create([
            'user_id' => auth('api')->id(),
            'image' => $upload->path,
            'wallet_type_id' => $request->wallet_type_id,
            'wallet_address' => $request->wallet_address,
            'coin' => $request->coin,
            'fee' => $fee,
            'real_coin' => $real_coin,
        ]);
        // 从用户钱包扣除对应金额
        $remark = "用户提币扣除" . $request->coin . ',手续费' . $fee . ',实际到账' . $real_coin;
        $logService = app()->make(LogService::class); // 钱包服务初始化
        $logService->userLog($user->id, $request->wallet_type_id, -$request->coin, 0, $day, UserWalletLog::FROM_WITHDRAW, $remark);

        $data['message'] = "提币申请提交成功,请等待审核!";
        return response()->json($data, 200);
    }

    /**
     * 我的提币
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function my(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'wallet_type_id' => 'required|exists:wallet_types,id', // 钱包类型
        ]);

        // 需要实名认证 TODO
        if ($user->is_verify == 0) {
            $data['message'] = "请先到会员中心进行实名认证！";
            return response()->json($data, 403);
        }
        if (empty($user->money_password)) {
            $data['message'] = "请先到会员中心设置资金密码！";
            return response()->json($data, 403);
        }

        // wallet_type_id 钱包类型/slug/获取对应钱包 TODO
        $wallet_type = WalletType::find($request->wallet_type_id);
        if (empty($wallet_type) || $wallet_type->is_enblened = 0) {
            $data['message'] = "不支持该类型！";
            return response()->json($data, 403);
        }
        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);
        $balance = $wallet->balanceFloat;

        $min = config('withdraw.min'); // 最小提币金额
        $fee = config('withdraw.coin_fee'); // 提币手续费

        if ($balance < $min) {
            $data['message'] = "当前账户" . $name . "币少于最小提币金额!";
            return response()->json($data, 404);
        }

        $data = [
            'balance' => $balance,
            'min' => $min,
            'fee' => $fee,
            'name' => $name,
        ];

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Pledge $pledge
     * @return \Illuminate\Http\Response
     */
    public function show(Pledge $pledge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Pledge $pledge
     * @return \Illuminate\Http\Response
     */
    public function edit(Pledge $pledge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Pledge $pledge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pledge $pledge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Pledge $pledge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pledge $pledge)
    {
        //
    }
}
