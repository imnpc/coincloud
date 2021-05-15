<?php

namespace App\Admin\Controllers;

use App\Models\DayFreed;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DayFreedController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '每日线性释放';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DayFreed());

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
            $filter->equal('freed_id', __('Freed id'));
        });

        $grid->column('id', __('Id'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user.mobile', __('Users'));
        $grid->column('freed_id', __('Freed id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('day', __('Day'));
        $grid->column('coin', __('释放数量'));
        $grid->column('today', __('第几天'));
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
        $show = new Show(DayFreed::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('freed_id', __('Freed id'));
        $show->field('product_id', __('Product id'));
        $show->field('day', __('Day'));
        $show->field('coin', __('Coin'));
        $show->field('today', __('Today'));
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
        $form = new Form(new DayFreed());

        $form->number('user_id', __('User id'));
        $form->number('freed_id', __('Freed id'));
        $form->number('product_id', __('Product id'));
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('coin', __('Coin'));
        $form->number('today', __('Today'));

        return $form;
    }
}
