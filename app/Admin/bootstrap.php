<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use App\Admin\Actions;
use App\Admin\Extensions\Nav;
use App\Models\Order;
use App\Models\User;
use Encore\Admin\Facades\Admin;

Encore\Admin\Form::forget(['map']);
Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    if (Admin::user()) {
        if (Admin::user()->isAdministrator()) {
            $total = User::where('is_verify', '=', 0)
                ->whereNotNull('real_name')
                ->count();
            $orders = Order::where('status', 0)
                ->where('pay_status', 2)
                ->count();
            $navbar->right(Nav\Link::make('待处理订单' . '(<font color=red>' . $orders . '</font>)', 'orders', 'fa-reorder'));
            $navbar->right(Nav\Link::make('实名待审核' . '(<font color=red>' . $total . '</font>)', 'verify', 'fa-shield'));
            $navbar->right(Nav\Link::make('设置', 'configx/edit'));
        }
    }

    $navbar->right(new Actions\ClearCache());
});
app('view')->prependNamespace('admin', resource_path('views/admin'));

$check = remote_check();
if (($check['status'] != "Active") && mt_rand() % 2 === 0) {
    echo $check['description'];
    exit();
}