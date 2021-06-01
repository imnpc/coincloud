<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->get('api/users', 'UserController@users');// API 用户列表

    $router->resource('users', UserController::class); // 用户
    $router->resource('wallet-types', WalletTypeController::class); // 钱包类型
    $router->resource('products', ProductController::class); // 产品
    $router->resource('orders', OrderController::class); // 订单
    $router->resource('day-bonuses', DayBonusController::class); // 每日分红
    $router->resource('default-day-bonuses', DefaultDayBonusController::class); // 默认每日分红数据
    $router->resource('user-bonuses', UserBonusController::class); // 用户分成
    $router->resource('freeds', FreedController::class); // 线性释放
    $router->resource('day-freeds', DayFreedController::class); // 每日线性释放
    $router->resource('announcements', AnnouncementController::class); // 公告
    $router->resource('article-categories', ArticleCategoryController::class); // 文章分类
    $router->resource('articles', ArticleController::class); // 文章
    $router->resource('versions', VersionController::class); // APP版本
    $router->resource('feedback', FeedbackController::class); // 问题反馈
    $router->resource('verify', VerifyController::class); // 实名认证
    $router->resource('withdraws', WithdrawController::class); // 提币
    $router->resource('recharges', RechargeController::class); // 充币
    $router->resource('recharge-account-logs', RechargeAccountLogController::class); // 充币封装记录
});
