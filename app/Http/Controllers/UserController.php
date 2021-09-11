<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\PhoneNumber;
use App\Exceptions\InvalidRequestException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (empty($request->parent_id)) {
            session()->flash('error', '邀请码不正确');
        }

        $parent_id = $request->parent_id;

        return view('user.register', compact('parent_id'));
    }

    /**
     * 手机号注册用户(需要处理邀请码)
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|phone:CN,mobile|unique:users,mobile',
            'captcha' => 'required',
            'verify_code' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
            'parent_id' => 'required|string',
        ]);
        // print_r($request->all());

        $mobile = $request->phone;
        $key = 'verificationCode_' . $mobile;

        $verifyData = \Cache::get($key);
        if (!$verifyData) {
            return back()->withErrors(['短信验证码已失效！'])->withInput();
        }
        if (!hash_equals($verifyData['code'], $request->verify_code)) {
            return back()->withErrors(['短信验证码不正确！'])->withInput();
        }
        $decode_id = \Hashids::decode($request->parent_id);// 解密传递的 ID
        if (empty($decode_id)) {
            return back()->withErrors(['邀请码不正确！'])->withInput();
        }

        $parent_id = $decode_id[0];// 解密后的 ID

        // 创建用户 TODO
        $user = User::create([
            'mobile' => $mobile,
            'name' => $mobile,
            'nickname' => $mobile,
            //'email' => $mobile . '@qq.com',
            'password' => bcrypt($request->password),
            'parent_id' => $parent_id,
        ]);
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('user.show', [$user]);
        return redirect()->route('download');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // 图片需要使用 OSS 来获取
        $config['reg_qrcode'] = Storage::disk(config('filesystems.default'))->url(config('user.reg_qrcode'));
        $config['download_url'] = config('user.download_url');

        return view('user.show', compact('user', 'config'));
    }

    /**
     * 直接下载页面
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function download()
    {
        // 图片需要使用 OSS 来获取
        $config['reg_qrcode'] = Storage::disk(config('filesystems.default'))->url(config('user.reg_qrcode'));
        $config['download_url'] = config('user.download_url');

        return view('user.download', compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
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
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * 发送验证码短信
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendcode(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|phone:CN,mobile|unique:users,mobile',
            'captcha' => 'required|captcha',
        ]);

        // 格式化手机号 去除 +86 去除空格
        $phone = ltrim(phone($request->phone, 'CN', 'E164'), '+86');

        $mobile = $phone;

        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0

        $key = 'verificationCode_' . $mobile;
        $expiredAt = now()->addMinutes(30);

        $verifyData = \Cache::get($key);
        if ($verifyData) {
            $data['message'] = "已经发送过验证码了";
            return response()->json($data, 200);
        }

        //Notification::route('mail', $request->email)->notify(new EmailVerify($code));// 发送邮件验证码
//        send_sms($mobile, $code);
        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($mobile)
        )->notify(new VerificationCode($code));// 发送短信验证码

        \Cache::put($key, ['mobile' => $mobile, 'code' => $code], $expiredAt); // 缓存验证码 30 分钟过期。

        $data['message'] = "验证码发送成功";
        return response()->json($data, 200);
    }
}
