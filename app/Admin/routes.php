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
});
