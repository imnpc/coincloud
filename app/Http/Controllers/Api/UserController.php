<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderResource;
use App\Models\DayBonus;
use App\Models\DayReward;
use App\Models\Freed;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use Bavix\Wallet\Interfaces\Mathable;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }


    /**
     * 当前登录用户详细信息
     * @param Request $request
     * @return User|mixed
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * 我的 个人中心
     * @param Request $request
     * @return array
     */
    public function my(Request $request)
    {
        $user = $request->user();

        $list = Product::where('status', '=', 0)
            ->orderBy('id', 'asc')
            ->select('id', 'wallet_type_id')
            ->get();
        foreach ($list as $k => &$v) {
            $list[$k]['wallet_slug_text'] = $v->wallet_slug . '资产';
        }

        $data = [
            'id' => $user->id,
            'mobile' => $user->mobile, // 手机号码
//            'nickname' => $user->nickname, // 昵称
            'name' => $user->name, // 账户
            'is_verify' => $user->is_verify, // 是否实名认证 0-未认证 1-已认证
            'power_list' => $list,
        ];

        return $data;
    }

    /**
     * 算力管理
     * @param Request $request
     * @return array
     */
    public function mypower(Request $request)
    {
        $user = $request->user();

        $data = [
            'total_power' => $user->cloud_power, // 总存储空间
            'wait_power' => 0, // 等待期算力
            'max_power' => $user->cloud_valid_power, // 上限有效算力
            'valid_power' => $user->cloud_valid_power, // 目前有效算力
            'progress' => @number_fixed($user->cloud_valid_power / $user->cloud_power * 100, 2), // 进度
            'chia_total_power' => $user->chia_power, // 起亚算力
            'chia_wait_power' => 0, // 起亚算力
            'chia_max_power' => $user->chia_power, // 上限有效算力
            'chia_valid_power' => $user->chia_power, // 目前有效算力
        ];

        return $data;
    }

    /**
     * 资产管理
     * @param Request $request
     * @return array
     */
    public function account(Request $request)
    {
        // wallet_type_id
        $user = $request->user();
        $day = Carbon::now()->subDay()->toDateString();// 获得前一天日期

        // wallet_type_id 钱包类型/slug/获取对应钱包 TODO
        $wallet_type = WalletType::find($request->wallet_type_id);
        if (empty($wallet_type) || $wallet_type->is_enblened = 0) {
            $data['message'] = "不支持该类型！";
            return response()->json($data, 403);
        }
        $product = Product::where('wallet_type_id', $request->wallet_type_id)
            ->where('status', 0)
            ->first();

        if (empty($product)) {
            $data['message'] = "产品不存在或者未启用！";
            return response()->json($data, 403);
        }

        $bonus = UserBonus::where('day', '=', $day)
            ->where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->first();
        // 临时禁用 TODO
//        if (!$bonus) {
//            $data['message'] = "Data Not Found.";
//            return response()->json($data, 404);
//        }

        $system = DayBonus::find($bonus->day_bonus_id);

        $name = $wallet_type->slug;
        $wallet = $user->getWallet($name);
        $balance = $wallet->balanceFloat; // 钱包余额
        //echo $balance;
        // 昨日增加
        $yesterday_add = $wallet->transactions()
            ->where('type', '=', Transaction::TYPE_DEPOSIT)
            ->whereDate('created_at', '=', Carbon::yesterday())
//            ->whereDate('created_at', '=', '2021-05-08')
            ->sum('amount');
        if ($yesterday_add <= 0) {
            $yesterday_add = 0;
        } else {
            $math = app(Mathable::class);
            $decimalPlaces = app(WalletService::class)->decimalPlaces($wallet);
            $yesterday_add = $math->div($yesterday_add, $decimalPlaces);
        }


        $wait_reward = 0;
        $total_reward = 0;
        $my_pledge = 0;
        $pledge_day = 0;

        // 查询奖励币
        $reward = Reward::where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->first();
        if ($reward) {
            $wait_reward = $reward->wait_coin;
            $total_reward = $reward->coin_freed;
        }

        // 昨日获得的奖励币
        $day_reward = DayReward::where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->where('day', '=', $day)
            ->sum('coin');

        // 75% 未释放 总计
        $coin_unfreed_day = Freed::where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->sum('wait_coin');

        // 质押币
        $pledge = Pledge::where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->first();
        $now = Carbon::now();
        if ($pledge) {
            $my_pledge = $pledge->coins;// 自己的质押币数量
            // 倒计时天数
            $created_at = $pledge->created_at;
            $check_day = $created_at->diffInDays($now); // 已经过去天数
            $pledge_day = $pledge->pledge_days - $check_day;
            if ($pledge_day <= 0) {
                $pledge_day = 0;
            }
        }

        $coins_total = UserBonus::where('user_id', '=', auth('api')->id())
            ->where('product_id', $product->id)
            ->sum('coins'); // 累计产出

        $data = [
            'coin_balance' => $balance, //  可提币账户
            'yesterday_coin' => $yesterday_add, // 昨日增加
            'day_reward' => $day_reward, // 奖励币当日释放
            'wait_reward' => $wait_reward, // 奖励币未释放
            'coin_freed_day' => (string)($bonus->coin_freed_day + $bonus->coin_freed_other), // 当日 75 % 已释放总量
            'coin_unfreed_day' => (string)$coin_unfreed_day, //  75 % 未释放累计
            'coin_rate_day' => (string)$bonus->coin_now, // 当日 25 % 释放数量
            'pledge_coin' => $my_pledge, // 质押币
            'pledge_day' => $pledge_day, // 质押剩余天数
            'cost' => $system->efficiency - $system->cost, // GAS消耗  = 挖矿效率 - 挖矿成本
            'coins_total' => $coins_total, // 累计产出
            'coin_type_id' => $request->wallet_type_id,
            'reward_from_id' => UserWalletLog::FROM_REWARD, // 奖励币明细 from
            'freed75_from_id' => UserWalletLog::FROM_FREED75, // 75%明细 from
            'freed25_from_id' => UserWalletLog::FROM_FREED, // 25%明细 from
            'pledge_from_id' => UserWalletLog::FROM_PLEDGE, // 质押币 明细
//            'gas_from_id' => UserWalletLog::FROM_BORROW,
        ];

        return $data;
    }

    /**
     * 我的订单
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function myorder(Request $request)
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $orders = QueryBuilder::for(Order::class)
            ->defaultSort('-created_at')
            ->with('product')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'product_id', 'order_sn', 'power', 'pay_money')
            ->paginate();
        foreach ($orders as $key => $value) {
            $orders[$key]['product_name'] = $value->product['name'];
            unset($value->product);
        }

        return OrderResource::collection($orders);
    }

    /**
     * 钱包流水日志
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function walletLog(Request $request)
    {
        $user = $request->user();
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $logs = QueryBuilder::for(UserWalletLog::class)
            ->allowedFilters([
                AllowedFilter::exact('type'),// 类型 0-现金 1-FIL币 2-抵押的FIL币 3-奖励币 4-股东分红 5-冻结的FIL币 6-收入的FIL币 7-冻结的奖励币 8-推荐分红
                AllowedFilter::exact('from'),// 来源 0-正常 1-推荐 2-股东分红 3-转入 4-转出 5-线性释放 6-每日分红 7-奖励币 8-提币 9-提现 10-借币 11-75% 线性释放 12-质押币
            ])
            ->defaultSort('-id')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'type', 'from', 'day', 'add', 'remark', 'created_at')
            ->paginate();

        return UserWalletLogResource::collection($logs);
    }

    /**
     * 推广算力流水日志
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function RewardwalletLog(Request $request)
    {
        $user = $request->user();
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $logs = QueryBuilder::for(UserWalletLog::class)
            ->defaultSort('-id')
            ->where('user_id', '=', auth('api')->id())
            ->where('type', '=', User::BALANCE_FILECOIN)
            ->whereIn('from', [UserWalletLog::FROM_REWARD_FREED, UserWalletLog::FROM_REWARD_FREED75])
            ->select('id', 'type', 'from', 'day', 'add', 'remark', 'created_at')
            ->paginate();

        return UserWalletLogResource::collection($logs);
    }

    /**
     * 发送验证码短信
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sms(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|exists:users',
        ]);

        $mobile = $request->mobile;
        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0
        //Notification::route('mail', $request->email)->notify(new EmailVerify($code));// 发送邮件验证码
        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile)
        )->notify(new VerificationCode($code));// 发送短信验证码

        $key = 'verificationCode_' . $mobile;
        $expiredAt = now()->addMinutes(30);

        $verifyData = \Cache::get($key);
        if ($verifyData) {
            abort(403, '已经发送过验证码了');
        }

        \Cache::put($key, ['mobile' => $mobile, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。

        $data['message'] = "验证码发送成功";
        return response()->json($data, 200);
    }

    /**
     * 找回密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
//            'mobile' => 'required|numeric|exists:users',
//            'code' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed', // 需要字段 password_confirmation
        ]);

        $user = $request->user();
//        $mobile = $user->mobile;
//        $key = 'verificationCode_' . $mobile;
//
//        $verifyData = \Cache::get($key);
//        if (!$verifyData) {
//            abort(403, '验证码已失效');// 验证码失效
//        }
//        if (!hash_equals($verifyData['code'], $request->code)) {
//            abort(401, '验证码不正确');// 验证码不正确
//        }

        // 查询该手机号用户 重设密码  bcrypt($request->password)
        //$user = User::where('mobile', '=', $user->mobile)->first();
        if ($user) {
            $user->update(['password' => bcrypt($request->password)]);
            $data['message'] = "密码重设成功";
            return response()->json($data, 200);
        } else {
            $data['message'] = "用户不存在";
            return response()->json($data, 404);
        }
    }

    /**
     * 我的团队
     * @param Request $request
     * @return array
     */
    public function team(Request $request)
    {
        $user = $request->user();
        $data = [];
        $commission = '0';
        $dividends = '0';
        $cash = '0';
        //cloud_power_affiliate1
        $data['cloud_power_affiliate1'] = $user->cloud_power_affiliate1;
        $data['cloud_power_affiliate2'] = $user->cloud_power_affiliate2;
        // 佣金 commission
        $commission = $user->cloud_power_reward;
        $data['commission'] = $commission;
        // 股东分红 dividends
        $dividends = UserWalletLog::where('user_id', '=', auth('api')->id())
            ->where('type', '=', User::BALANCE_FILECOIN)
            ->where('from', '=', UserWalletLog::FROM_TEAM_DIVIDENDS)
            ->sum('add');
        $data['dividends'] = $dividends;
        // 奖励产币
        $cash = UserWalletLog::where('user_id', '=', auth('api')->id())
            ->where('type', '=', User::BALANCE_FILECOIN)
            ->where('from', '=', UserWalletLog::FROM_REWARD_DAY)
            ->sum('add');
        $data['cash'] = $cash;
        // 我的账户  下级下单记录
        // 查询我的下级
        $parent = User::with('sons')
            ->find(auth('api')->id())
            ->toArray();
        $users_son1 = [];

        foreach ($parent['sons'] as $key => $value) {
            $users_son1[] = $value['id'];
        }
        $users1 = array_values($users_son1);
        $orders1_total = 0;
        $orders2_total = 0;

        // 查询 1 级订单
        $orders1 = QueryBuilder::for(Order::class)
            ->defaultSort('-created_at')
            ->with('user')
            ->whereIn('user_id', $users1)
            ->where('product_id', '=', 3)
            ->where('pay_status', '=', Order::PAID_COMPLETE)
            ->select('id', 'user_id', 'power', 'product_id', 'pay_status', 'created_at')
            ->get();
        foreach ($orders1 as $key => $value) {
            $orders1[$key]['username'] = $value->user['nickname'];
            $orders1_total += $value['power'];
            unset($value->user);
        }

        $orders2 = QueryBuilder::for(Order::class)
            ->defaultSort('-created_at')
            ->with('user')
            ->whereIn('user_id', $users1)
            ->where('product_id', '=', 4)
            ->where('pay_status', '=', Order::PAID_COMPLETE)
            ->select('id', 'user_id', 'power', 'product_id', 'pay_status', 'created_at')
            ->get();
        foreach ($orders2 as $key => $value) {
            $orders2[$key]['username'] = $value->user['nickname'];
            $orders2_total += $value['power'];
            unset($value->user);
        }

        $data['orders1_total'] = $orders1_total;
        $data['orders_level1'] = $orders1;
        $data['orders2_total'] = $orders2_total;
        $data['orders_level2'] = $orders2;

        return $data;
    }

    // 上传和修改头像
    public function avatar(Request $request)
    {
        $image = image_ext(); // 上传图片类型
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|mimes:' . $image, // 头像
        ]);

        if ($request->file('avatar')) {
            $image = upload_images($request->file('avatar'), 'avatar', $user->id);
            $attributes['avatar'] = $image->path;
            $avatar_image_id = $image->id;
        }
        $user->update($attributes);

        //查询和清理多余头像
        if ($avatar_image_id > 0) {
            $avatars = DB::table('images')->where('id', '!=', $avatar_image_id)
                ->where('type', '=', 'avatar')
                ->where('user_id', '=', $user->id)
                ->get();
            foreach ($avatars as $avatar) {
                Storage::disk($avatar->disk)->delete($avatar->path);
                DB::table('images')->where('id', '=', $avatar->id)->delete();
            }
        }

        $data['message'] = "头像上传成功";
        return response()->json($data, 200);
    }

    /**
     * 用户实名认证
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $image = image_ext(); // 上传图片类型
        $user = $request->user();

        $request->validate([
            'real_name' => 'required|string', // 真实姓名
            'id_number' => 'required|numeric', // 身份证号码
            'id_front' => 'required|mimes:' . $image, // 身份证正面
            'id_back' => 'required|mimes:' . $image, // 身份证反面
        ]);

        if ($user->is_verify == 1) {
            $data['message'] = "已经实名认证过了";
            return response()->json($data, 403);
        }

        $attributes = $request->only([
            'real_name', 'id_number', 'id_front', 'id_back'
        ]);

        if ($request->file('id_front')) {
            $upload = upload_images($request->file('id_front'), 'verify', $user->id);
            $attributes['id_front'] = $upload->path;
        }
        if ($request->file('id_back')) {
            $upload2 = upload_images($request->file('id_back'), 'verify', $user->id);
            $attributes['id_back'] = $upload2->path;
        }

        $user->update($attributes);

        $data['message'] = "实名认证信息提交成功,请等待审核";
        return response()->json($data, 200);
    }

    /**
     * 邀请码
     * @param Request $request
     * @return array
     */
    public function invite(Request $request)
    {
        $data = [];
        $user = $request->user();

        // 实名 不实名无法推荐 TODO
        if ($user->is_verify == 0) {
            $data['message'] = "请先到会员中心进行实名认证！";
            return response()->json($data, 403);
        }

        $code = \Hashids::encode($user->id);
        $url = url("/users/register?parent_id={$code}");
        $data['code'] = $code;
        $data['url'] = $url;

        $path = 'qrcode/' . $code . '.png'; // 二维码图片名称路径
        $exists = Storage::disk('oss')->exists($path); // 查询文件是否存在
        if (!$exists) {
            // 不存在就生成并上传二维码图片
            //$qr = QrCode::format('png')->merge('https://cloudimg.xhkylm.com/logo120.png', .3, true)->size(300)->errorCorrection('H')->generate($url);
            $qr = QrCode::format('png')->size(300)->errorCorrection('H')->generate($url);
            Storage::disk('oss')->put($path, $qr); //上传到 OSS
        }

        $data['qrcode'] = Storage::disk('oss')->url($path); // 返回图片 URL

        return $data;
    }
}
