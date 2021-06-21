<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\WeeklyLog;
use App\Models\Weekly;
use Encore\Admin\Actions\Action;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WeeklyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '每周统计报表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Weekly());

        $grid->column('id', __('Id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Product'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('product.wallet_slug', __('Wallet slug'));
        $grid->column('year', __('Year'));
        $grid->column('week', __('Week'));
        $grid->column('begin', __('Begin'));
        $grid->column('end', __('End'));
        $grid->column('begin_time', __('Begin time'));
        $grid->column('end_time', __('End time'));
        $grid->column('freed', __('Freed'));
        $grid->column('freed75', __('Freed75'));
        $grid->column('reward', __('Reward'));
        $grid->column('total', __('Total'));
        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        //$grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->disableCreateButton(); // 禁用创建按钮

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
            $actions->disableEdit();// 去掉编辑
            $actions->add(new WeeklyLog);
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
        $show = new Show(Weekly::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('year', __('Year'));
        $show->field('week', __('Week'));
        $show->field('begin', __('Begin'));
        $show->field('end', __('End'));
        $show->field('begin_time', __('Begin time'));
        $show->field('end_time', __('End time'));
        $show->field('freed', __('Freed'));
        $show->field('freed75', __('Freed75'));
        $show->field('reward', __('Reward'));
        $show->field('total', __('Total'));
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
        $form = new Form(new Weekly());

        $form->number('product_id', __('Product id'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->text('year', __('Year'));
        $form->text('week', __('Week'));
        $form->text('begin', __('Begin'));
        $form->text('end', __('End'));
        $form->text('begin_time', __('Begin time'));
        $form->text('end_time', __('End time'));
        $form->decimal('freed', __('Freed'));
        $form->decimal('freed75', __('Freed75'));
        $form->decimal('reward', __('Reward'));
        $form->decimal('total', __('Total'));

        return $form;
    }
}
