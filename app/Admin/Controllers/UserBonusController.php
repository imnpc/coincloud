<?php

namespace App\Admin\Controllers;

use App\Models\UserBonus;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserBonusController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户每日分成';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserBonus());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器 Order::TYPE_
            $filter->equal('user_id', __('User id'));
        });

        $grid->column('id', __('Id'));
        $grid->column('day', __('Day'));
//        $grid->column('bonus_id', __('Bonus id'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user.mobile', __('Users'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
//        $grid->column('bonus_coin_add', __('Bonus coin add'));
        $grid->column('valid_power', __('Valid power'));
        $grid->column('each_add', __('Each add'));
        $grid->column('coins', __('Coins'));
        $grid->column('pay_user_rate', __('Pay user rate'));
        $grid->column('coin_for_user', __('Coin for user'));
//        $grid->column('now_rate', __('Now rate'));
        $grid->column('coin_now', __('Coin now'));
//        $grid->column('freed_rate', __('Freed rate'));
        $grid->column('coin_freed', __('Coin freed'));
        $grid->column('coin_freed_day', __('Coin freed day'));
        $grid->column('coin_freed_other', __('Coin freed other'));
        $grid->column('coin_day', __('Coin day'));
//        $grid->column('balance', __('Balance'));
//        $grid->column('parent1_uid', __('Parent1 uid'));
//        $grid->column('parent1_rate', __('Parent1 rate'));
//        $grid->column('coin_parent1', __('Coin parent1'));
//        $grid->column('parent2_uid', __('Parent2 uid'));
//        $grid->column('parent2_rate', __('Parent2 rate'));
//        $grid->column('coin_parent2', __('Coin parent2'));
//        $grid->column('bonus_rate', __('Bonus rate'));
//        $grid->column('coin_bonus', __('Coin bonus'));
//        $grid->column('risk_rate', __('Risk rate'));
//        $grid->column('coin_risk', __('Coin risk'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
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
        $show = new Show(UserBonus::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('day', __('Day'));
        $show->field('bonus_id', __('Bonus id'));
        $show->field('user_id', __('User id'));
        $show->field('product_id', __('Product id'));
        $show->field('bonus_coin_add', __('Bonus coin add'));
        $show->field('valid_power', __('Valid power'));
        $show->field('each_add', __('Each add'));
        $show->field('coins', __('Coins'));
        $show->field('pay_user_rate', __('Pay user rate'));
        $show->field('coin_for_user', __('Coin for user'));
        $show->field('now_rate', __('Now rate'));
        $show->field('coin_now', __('Coin now'));
        $show->field('freed_rate', __('Freed rate'));
        $show->field('coin_freed', __('Coin freed'));
        $show->field('coin_freed_day', __('Coin freed day'));
        $show->field('coin_freed_other', __('Coin freed other'));
        $show->field('coin_day', __('Coin day'));
        $show->field('balance', __('Balance'));
        $show->field('parent1_uid', __('Parent1 uid'));
        $show->field('parent1_rate', __('Parent1 rate'));
        $show->field('coin_parent1', __('Coin parent1'));
        $show->field('parent2_uid', __('Parent2 uid'));
        $show->field('parent2_rate', __('Parent2 rate'));
        $show->field('coin_parent2', __('Coin parent2'));
        $show->field('bonus_rate', __('Bonus rate'));
        $show->field('coin_bonus', __('Coin bonus'));
        $show->field('risk_rate', __('Risk rate'));
        $show->field('coin_risk', __('Coin risk'));
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
        $form = new Form(new UserBonus());

        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->number('bonus_id', __('Bonus id'));
        $form->number('user_id', __('User id'));
        $form->number('product_id', __('Product id'));
        $form->decimal('bonus_coin_add', __('Bonus coin add'));
        $form->decimal('valid_power', __('Valid power'));
        $form->decimal('each_add', __('Each add'));
        $form->decimal('coins', __('Coins'));
        $form->decimal('pay_user_rate', __('Pay user rate'));
        $form->decimal('coin_for_user', __('Coin for user'));
        $form->decimal('now_rate', __('Now rate'));
        $form->decimal('coin_now', __('Coin now'));
        $form->decimal('freed_rate', __('Freed rate'));
        $form->decimal('coin_freed', __('Coin freed'));
        $form->decimal('coin_freed_day', __('Coin freed day'));
        $form->decimal('coin_freed_other', __('Coin freed other'));
        $form->decimal('coin_day', __('Coin day'));
        $form->decimal('balance', __('Balance'));
        $form->number('parent1_uid', __('Parent1 uid'));
        $form->decimal('parent1_rate', __('Parent1 rate'));
        $form->decimal('coin_parent1', __('Coin parent1'));
        $form->number('parent2_uid', __('Parent2 uid'));
        $form->decimal('parent2_rate', __('Parent2 rate'));
        $form->decimal('coin_parent2', __('Coin parent2'));
        $form->decimal('bonus_rate', __('Bonus rate'));
        $form->decimal('coin_bonus', __('Coin bonus'));
        $form->decimal('risk_rate', __('Risk rate'));
        $form->decimal('coin_risk', __('Coin risk'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
