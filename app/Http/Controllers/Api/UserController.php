<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderResource;
use App\Http\Resources\UserWalletLogResource;
use App\Models\DayBonus;
use App\Models\DayReward;
use App\Models\Freed;
use App\Models\Order;
use App\Models\Pledge;
use App\Models\Product;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserBonus;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use App\Notifications\VerificationCode;
use App\Services\UserWalletService;
use Bavix\Wallet\Interfaces\Mathable;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Mews\Captcha\Captcha;
use Notification;
use Overtrue\EasySms\PhoneNumber;
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
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化

        $list = Product::where('status', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($list as $k => $v) {
            $data[$k]['name'] = $v->wallet_slug;
            // 收益信息
            $data[$k]['freed'] = UserBonus::where('user_id', '=', $user->id)
                ->where('product_id', $v->id)
                ->sum('coin_day'); // 已到账  = 25% 立即释放 + 已经线性释放
            $data[$k]['unfreed'] = Freed::where('user_id', '=', $user->id)
                ->where('product_id', $v->id)
                ->sum('wait_coin'); // 等待到账的 180天 线性释放的
            // 挖矿数据
            $data[$k]['vaild_power'] = Order::where('user_id', '=', $user->id)
                ->where('product_id', $v->id)
                ->where('pay_status', 0)
                ->where('status', 0)
                ->sum('valid_power'); // 有效算力
            $data[$k]['max_valid_power'] = Order::where('user_id', '=', $user->id)
                ->where('product_id', $v->id)
                ->where('pay_status', 0)
                ->where('status', 0)
                ->sum('number'); // 全部算力
            $data[$k]['yesterday_add'] = $UserWalletService->yesterday($user->id, $v->wallet_type_id); // 昨日收益
            $data[$k]['total_revenue'] = $UserWalletService->total($user->id, $v->wallet_type_id); // 累计收益
            // 全网统计
            $data[$k]['network_revenue'] = $v->network_revenue; // 全网24小时产出
            $data[$k]['network_average_revenue'] = $v->network_average_revenue; // 24小时平均挖矿收益
            $data[$k]['network_valid_power'] = $v->network_valid_power; // 全网有效算力
            $data[$k]['network_basic_rate'] = $v->network_basic_rate; // 当前基础费率
            $data[$k]['wallet_type_id'] = $v->wallet_type_id;
            $data[$k]['freed_from_id'] = UserWalletLog::FROM_FREED;
            $data[$k]['unfreed_from_id'] = UserWalletLog::FROM_FREED75;
            $data[$k]['is_show_text'] = $v->is_show_text;

        }

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
        $UserWalletService = app()->make(UserWalletService::class); // 钱包服务初始化
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
        $yesterday_add = $UserWalletService->yesterday($user->id, $request->wallet_type_id);

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
            $my_pledge = $pledge->pledge_coins;// 自己的质押币数量
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
            ->select('id', 'product_id', 'order_sn', 'number', 'pay_money')
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
                AllowedFilter::exact('wallet_type_id'), // 钱包类型 ID
                AllowedFilter::exact('from'),// 来源
            ])
            ->defaultSort('-id')
            ->where('user_id', '=', auth('api')->id())
            ->select('id', 'wallet_type_id', 'from', 'day', 'add', 'remark', 'created_at')
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
        // 查询我的下级
        $parent = User::with('sons')
            ->find(auth('api')->id())
            ->toArray();
        $users_son1 = [];

        foreach ($parent['sons'] as $key => $value) {
            $users_son1[] = $value['id'];
        }
        $users1 = array_values($users_son1);

        $data = Product::where('status', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($data as $k => $v) {
            $orders1_total = 0;
            $list[$k]['name'] = $v->name;
            $list[$k]['wallet_slug'] = $v->wallet_slug;
            // 奖励算力 TODO
            $list[$k]['reward_power'] = 0;
            // 分红账户
            $bonus = UserWalletLog::where('user_id', '=', $user->id)
                ->where('wallet_type_id', '=', $v->wallet_type_id)
                ->where('from', '=', UserWalletLog::FROM_TEAM_DIVIDENDS)
                ->sum('add');
            $list[$k]['bonus'] = $bonus;
            // 奖励产币
            $reward = UserWalletLog::where('user_id', '=', $user->id)
                ->where('wallet_type_id', '=', $v->wallet_type_id)
                ->where('from', '=', UserWalletLog::FROM_COMMISSION)
                ->sum('add');
            $list[$k]['reward'] = $reward;
            // 查询 1 级订单
            $orders1 = QueryBuilder::for(Order::class)
                ->defaultSort('-created_at')
                ->with('user')
                ->whereIn('user_id', $users1)
                ->where('product_id', '=', $v->id)
                ->where('pay_status', '=', Order::PAID_COMPLETE)
                ->select('id', 'user_id', 'max_valid_power', 'product_id', 'pay_status', 'created_at')
                ->get();
            foreach ($orders1 as $key => $value) {
                $orders1[$key]['username'] = $value->user['nickname'];
                $orders1[$key]['order_time'] = $value->created_at->format('Y-m-d');
                $orders1_total += $value['max_valid_power'];
                unset($value->user);
            }
            $list[$k]['orders_list'] = $orders1;
//            $list[$k]['orders1_total'] = $orders1_total;
            $list[$k]['wallet_type_id'] = $v->wallet_type_id;
            $list[$k]['reward_power_from_id'] = 0;
            $list[$k]['bonus_from_id'] = UserWalletLog::FROM_TEAM_DIVIDENDS;
            $list[$k]['reward_from_id'] = UserWalletLog::FROM_COMMISSION;
        }

        return $list;
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

//        $path = 'qrcode/' . $code . '.png'; // 二维码图片名称路径
        if (config('app.env') == 'local') {
            $path = 'qrcode/dev/' . $code . '.png'; // 二维码图片名称路径 TODO
        } else {
            $path = 'qrcode/' . $code . '.png'; // 二维码图片名称路径
        }

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

    /**
     * 设置资金密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setMoneyPassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed', // 需要字段 password_confirmation
            'code' => 'required', // 短信验证码
        ]);
        $user = $request->user();
        $mobile = $user->mobile;
        $key = 'verificationCode_' . $mobile;

        $verifyData = \Cache::get($key);
        if (!$verifyData) {
            abort(403, '验证码已失效');// 验证码失效
        }

        if (!hash_equals($verifyData['code'], $request->code)) {
            abort(401, '验证码不正确');// 验证码不正确
        }

        $user->update(['money_password' => bcrypt($request->password)]);
        $data['message'] = "资金密码设置成功";
        return response()->json($data, 200);
    }

    /**
     * 向已登录用户发送短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function usersms(Request $request)
    {
        $user = $request->user();
        $mobile = $user->mobile;

        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0

        $key = 'verificationCode_' . $mobile;
        $expiredAt = now()->addMinutes(30);

        $verifyData = \Cache::get($key);
        if ($verifyData) {
            abort(403, '已经发送过验证码了');
        }

        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile)
        )->notify(new VerificationCode($code));// 发送短信验证码

        \Cache::put($key, ['mobile' => $mobile, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。

        $data['message'] = "验证码发送成功";
        return response()->json($data, 200);
    }

    // 修改昵称
    public function nickname(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nickname' => 'required|string|unique:users', // 昵称
        ]);

        $attributes['nickname'] = $request->nickname;
        $user->update($attributes);

        $data['message'] = "昵称修改成功";
        return response()->json($data, 200);
    }

    /**
     * 手机号重设密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function forgetPassword(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|exists:users',
            'code' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed', // 需要字段 password_confirmation
        ]);

        $mobile = $request->mobile;
        $key = 'verificationCode_' . $mobile;

        $verifyData = \Cache::get($key);
        if (!$verifyData) {
            abort(403, '短信验证码已失效');// 验证码失效
        }
        if (!hash_equals($verifyData['code'], $request->code)) {
            abort(401, '短信验证码不正确');// 验证码不正确
        }

        // 查询该手机号用户 重设密码  bcrypt($request->password)
        $user = User::where('mobile', '=', $request->mobile)->first();
        if ($user) {
            \Cache::forget($key);
            $user->update(['password' => bcrypt($request->password)]);
            $data['message'] = "密码重设成功";
            return response()->json($data, 200);
        } else {
            $data['message'] = "用户不存在";
            return response()->json($data, 404);
        }
    }

    /**
     * 使用手机号获取图形验证码
     * @param Request $request
     * @param Captcha $captchaBuilder
     * @return \Illuminate\Http\JsonResponse
     */
    public function captcha(Request $request, Captcha $captcha)
    {
        $this->validate($request, [
            'mobile' => 'required|numeric|exists:users',
        ]);

        $key = 'captcha-' . Str::random(15);
        $mobile = $request->mobile;

        $captcha = $captcha->create('flat', true);

        $expiredAt = now()->addMinutes(10); // 有效时间 10 分钟

        \Cache::put($key, [
            'mobile' => $mobile,
            'captcha' => $captcha['key'],
        ], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_img' => $captcha['img']
        ];

        return response()->json($result)->setStatusCode(201);
    }

    public function captchasms(Request $request)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            abort(403, '图片验证码已失效');
        }

        if (!captcha_api_check($request->captcha_code, $captchaData['captcha'], 'flat')) {
            Cache::forget($request->captcha_key);
            abort(403, '验证码错误');
        }

        $mobile = $captchaData['mobile'];

        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0

        $key = 'verificationCode_' . $mobile;
        $expiredAt = now()->addMinutes(30);

        $verifyData = \Cache::get($key);
        if ($verifyData) {
            abort(200, '已经发送过验证码了');
        }

        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile)
        )->notify(new VerificationCode($code));// 发送短信验证码

        \Cache::put($key, ['mobile' => $mobile, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。

        $data['message'] = "验证码发送成功";
        return response()->json($data, 200);
    }
}
