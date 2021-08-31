<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\UserWalletLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserWalletLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户钱包日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserWalletLog());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
            $filter->equal('wallet_type_id', __('Product'))->select(Product::all()->pluck('name', 'wallet_type_id'));
        });

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('user.nickname', __('用户'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('wallet_slug', __('Wallet slug'));
//        $grid->column('from_user_id', __('From user id'));
        $grid->column('day', __('Day'));
        $grid->column('old', __('Old'));
        $grid->column('add', __('Add'));
        $grid->column('new', __('New'));
        $grid->column('from', __('From'))->display(function ($value) {
            return UserWalletLog::$fromMap[$value];
        });
        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->disableCreateButton(); // 禁用创建按钮
        $grid->disableActions(); // 禁用行操作列

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
            $actions->disableEdit();// 去掉编辑
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(UserWalletLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('from_user_id', __('From user id'));
        $show->field('day', __('Day'));
        $show->field('old', __('Old'));
        $show->field('add', __('Add'));
        $show->field('new', __('New'));
        $show->field('from', __('From'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserWalletLog());

        $form->number('user_id', __('User id'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->number('from_user_id', __('From user id'));
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('old', __('Old'));
        $form->decimal('add', __('Add'));
        $form->decimal('new', __('New'));
        $form->switch('from', __('From'));
        $form->text('remark', __('Remark'));

        return $form;
    }
}
