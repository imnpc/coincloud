<?php

use App\Http\Controllers\Api\ElectricChargeLogController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\PledgeController;
use App\Http\Controllers\Api\RechargeController;
use App\Http\Controllers\Api\UtilsController;
use App\Http\Controllers\Api\WithdrawController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    //->namespace('Api') // laravel 8 api 需要去掉这个
    ->name('api.v1.')
    ->group(function () {

        Route::middleware('throttle:' . config('api.rate_limits.sign'))
            ->group(function () {
                Route::post('authorizations', [AuthorizationsController::class, 'store']); // 登录
                Route::put('authorizations/current', [AuthorizationsController::class, 'update']);// 刷新token
                Route::delete('authorizations/current', [AuthorizationsController::class, 'destroy']);// 删除token
            });

        Route::middleware('throttle:' . config('api.rate_limits.access'))
            ->group(function () {
                // 游客可以访问的接口
                Route::get('test', [IndexController::class, 'test']); // test
                Route::get('demo', [IndexController::class, 'demo']); // test

                Route::resource('announcement', AnnouncementController::class); // 公告
                Route::resource('article', ArticleController::class); // 文章系统
                Route::get('category', [ArticleController::class, 'category']); // 文章分类
                Route::get('index', [IndexController::class, 'index']); // APP 首页
                Route::get('aboutus', [IndexController::class, 'aboutus']); // 关于我们
                Route::get('agreement', [IndexController::class, 'agreement']); // 用户协议
                Route::get('version', [VersionController::class, 'index']); // 检测最新版本
                Route::post('checkversion', [VersionController::class, 'check']); // 比较版本号

//                Route::resource('product', ProductController::class); // 产品资源
                Route::get('product', [ProductController::class, 'index']); // 产品列表
                Route::get('product/{product}', [ProductController::class, 'show']); // 产品详情
                Route::post('sms', [UserController::class, 'sms']); // 发送短信

                Route::post('captcha', [UserController::class, 'captcha']); // 使用手机号获取图片验证码
                Route::post('captchasms', [UserController::class, 'captchasms']); // 使用图片验证码获取短信验证码
                Route::post('forget', [UserController::class, 'forgetPassword']); // 找回重置密码
                Route::post('reg', [UserController::class, 'store']); // 注册

                Route::post('emailCaptcha', [UserController::class, 'emailCaptcha']); // 使用邮箱获取图片验证码
                Route::post('captchaEmailCode', [UserController::class, 'captchaEmailCode']); // 使用邮箱获取短信验证码
                Route::post('emailForget', [UserController::class, 'emailForgetPassword']); // 找回重置密码
                Route::post('emailReg', [UserController::class, 'emailReg']); // 邮箱注册

                Route::post('huobiKline', [UtilsController::class, 'huobiKline']); // 火币K线数据
                Route::post('getHistoryTrade', [UtilsController::class, 'getHistoryTrade']); // 火币 获得近期交易记录
                Route::post('usdToCny', [UtilsController::class, 'usdToCny']); // 美元转人民币
                Route::post('getCandles', [UtilsController::class, 'getCandles']); // OKEX 获取交易产品K线数据
                Route::post('huobiDepth', [UtilsController::class, 'huobiDepth']); // 火币 市场深度数据
                Route::post('huobiTrade', [UtilsController::class, 'huobiTrade']); // 火币 最近市场成交记录
                Route::post('huobiDetail', [UtilsController::class, 'huobiDetail']); // 火币 最近24小时行情数据

                // 登录后可以访问的接口
                Route::middleware('auth:api')->group(function () {
                    Route::get('user', [UserController::class, 'me']); // 当前登录用户信息

                    Route::get('my', [UserController::class, 'my']); // 我的
                    Route::get('team', [UserController::class, 'team']); // 我的团队
                    Route::post('avatar', [UserController::class, 'avatar']); // 修改用户头像
                    Route::post('verify', [UserController::class, 'verify']); // 用户实名认证
                    Route::post('reset', [UserController::class, 'resetPassword']); // 重设密码
                    Route::get('invite', [UserController::class, 'invite']); // 邀请码

                    Route::post('moneypassword', [UserController::class, 'setMoneyPassword']); // 设置资金密码
                    Route::post('usersms', [UserController::class, 'usersms']); // 向已登录用户发送短信验证码

                    Route::get('mypower', [UserController::class, 'mypower']); // 算力管理
                    Route::get('myorder', [UserController::class, 'myorder']); // 我的订单
                    Route::get('account', [UserController::class, 'account']); // 我的资产
                    Route::get('walletlog', [UserController::class, 'walletLog']); // 我的资产流水
//            Route::get('rewardwalletlog', 'UserController@RewardwalletLog'); // 奖励算力资产流水
                    Route::get('myproduct', [UserController::class, 'myproduct']); // 我的产品列表
                    Route::get('myaccount', [UserController::class, 'myaccount']); // 我的资产列表

                    Route::resource('withdraw', WithdrawController::class); // 提币
                    Route::get('mycoin', [WithdrawController::class, 'my']); // 我的提币

//            Route::resource('withdrawmoney', 'WithdrawMoneyController'); // 提现
//            Route::get('mymoney', 'WithdrawMoneyController@my'); // 我的提现
//            Route::resource('bankcard', 'BankcardController'); // 银行卡
                    Route::resource('feedback', FeedbackController::class); // 问题反馈

                    Route::resource('order', OrderController::class); // 订单
                    Route::post('checkorder', [OrderController::class, 'check']); // 预览检测订单
                    Route::post('getprice', [OrderController::class, 'getprice']); // 获取价格列表
                    Route::patch('orders/{order}', [OrderController::class, 'update']); // 更新订单支付凭证

                    Route::resource('recharge', RechargeController::class); // 充值
                    Route::get('myrecharge', [RechargeController::class, 'my']); // 我的充值页面
                    Route::get('powerlog', [RechargeController::class, 'powerlog']); // 算力封装记录

                    Route::resource('electricChargeLog', ElectricChargeLogController::class); // 订单
                    Route::patch('electricCharge/{electricChargeLog}', [ElectricChargeLogController::class, 'update']); // 更新电费支付凭证
//            Route::resource('lend', 'LendController'); // 出借
//            Route::get('mylend', 'LendController@my'); // 我的出借页面

                    Route::resource('pledge', PledgeController::class); // 质押币
                });
            });
    });
