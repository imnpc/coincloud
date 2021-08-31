<?php

namespace App\Admin\Controllers;

use App\Models\Pledge;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PledgeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '质押币';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Pledge());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
        });

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('user.nickname', __('下单用户'));
        //$grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('order_id', __('Order id'));
        $grid->column('power', __('Power'));
        $grid->column('pledge_fee', __('每T基数'));
        $grid->column('pledge_coins', __('质押币总数'));
        $grid->column('pledge_days', __('质押天数'));
        $grid->column('wait_days', __('剩余天数'));
        //0-质押中 1-已完成退回
        $grid->column('status', __('Status'))->using([
            0 => '质押中',
            1 => '已完成退回',
        ], '未知')->label([
            0 => 'danger',
            1 => 'success',
        ], 'danger');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

//        $grid->disableFilter(); // 禁用查询过滤器
        $grid->disableRowSelector(); // 禁用行选择checkbox
        $grid->disableCreateButton(); // 禁用创建按钮
        $grid->disableActions(); // 禁用行操作列
        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器

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
        $show = new Show(Pledge::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('order_id', __('Order id'));
        $show->field('product_id', __('Product id'));
        $show->field('power', __('Power'));
        $show->field('pledge_fee', __('Pledge fee'));
        $show->field('coins', __('Coins'));
        $show->field('pledge_days', __('Pledge days'));
        $show->field('wait_days', __('Wait days'));
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
        $form = new Form(new Pledge());

        $form->number('user_id', __('User id'));
        $form->number('order_id', __('Order id'));
        $form->number('product_id', __('Product id'));
        $form->decimal('power', __('Power'));
        $form->text('pledge_fee', __('Pledge fee'));
        $form->decimal('coins', __('Coins'));
        $form->number('pledge_days', __('Pledge days'));
        $form->number('wait_days', __('Wait days'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
