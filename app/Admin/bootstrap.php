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
use App\Models\User;
use Encore\Admin\Facades\Admin;

Encore\Admin\Form::forget(['map']);
Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    if(Admin::user()){
        if(Admin::user()->isAdministrator()){
            $total = User::where('is_verify', '=', 0)
                ->whereNotNull('real_name')
                ->count();
            $navbar->right(Nav\Link::make('实名待审核'.'(<font color=red>'.$total.'</font>)', 'verify','fa-shield'));
            $navbar->right(Nav\Link::make('设置', 'configx/edit'));
        }
    }

    $navbar->right(new Actions\ClearCache());
});
app('view')->prependNamespace('admin', resource_path('views/admin'));
