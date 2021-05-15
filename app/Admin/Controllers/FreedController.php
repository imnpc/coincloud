<?php

namespace App\Admin\Controllers;

use App\Models\Freed;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FreedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '线性释放';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Freed());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
            $filter->equal('user_bonus_id', __('User bonus id'));
        });

        $grid->column('id', __('Id'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user_bonus_id', __('User bonus id'));
        $grid->column('user.mobile', __('Users'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('day', __('Day'));
        $grid->column('coins', __('Coins'));
        $grid->column('freed_rate', __('Freed rate'));
        $grid->column('coin_freed', __('Coin freed'));
        $grid->column('coin_freed_day', __('Coin freed day'));
        $grid->column('other_fee', __('Other fee'));
        $grid->column('days', __('Days'));
        $grid->column('already_day', __('Already day'));
        $grid->column('already_coin', __('Already coin'));
        $grid->column('wait_coin', __('Wait coin'));
//        $grid->column('status', __('Status'));
        // 0-释放中 1-释放完毕
        $grid->column('status', __('Status'))->using([
            0 => '释放中',
            1 => '释放完毕',
        ], '未知')->label([
            0 => 'danger',
            1 => 'success',
        ], 'danger');
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
        $show = new Show(Freed::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('user_bonus_id', __('User bonus id'));
        $show->field('product_id', __('Product id'));
        $show->field('day', __('Day'));
        $show->field('coins', __('Coins'));
        $show->field('freed_rate', __('Freed rate'));
        $show->field('coin_freed', __('Coin freed'));
        $show->field('coin_freed_day', __('Coin freed day'));
        $show->field('other_fee', __('Other fee'));
        $show->field('days', __('Days'));
        $show->field('already_day', __('Already day'));
        $show->field('already_coin', __('Already coin'));
        $show->field('wait_coin', __('Wait coin'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Freed());

        $form->number('user_id', __('User id'));
        $form->number('user_bonus_id', __('User bonus id'));
        $form->number('product_id', __('Product id'));
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('coins', __('Coins'));
        $form->number('freed_rate', __('Freed rate'));
        $form->decimal('coin_freed', __('Coin freed'));
        $form->decimal('coin_freed_day', __('Coin freed day'));
        $form->decimal('other_fee', __('Other fee'));
        $form->number('days', __('Days'));
        $form->number('already_day', __('Already day'));
        $form->decimal('already_coin', __('Already coin'));
        $form->decimal('wait_coin', __('Wait coin'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
