<?php

namespace App\Admin\Controllers;

use App\Models\DefaultDayBonus;
use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DefaultDayBonusController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '默认分红数据';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DefaultDayBonus());

        $grid->column('id', __('Id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('power_add', __('Power add'));
        $grid->column('coin_add', __('Coin add'));
        $grid->column('efficiency', __('Efficiency'));
        $grid->column('cost', __('Cost'));
        $grid->column('fee', __('Fee'));
        $grid->column('day_price', __('Day price'));
        $grid->column('day_pledge', __('Day pledge'));
        $grid->column('day_cost', __('Day cost'));
//        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->disableFilter(); // 禁用查询过滤器
        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
//            $actions->disableEdit();// 去掉编辑
        });
        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

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
        $show = new Show(DefaultDayBonus::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('power_add', __('Power add'));
        $show->field('coin_add', __('Coin add'));
        $show->field('efficiency', __('Efficiency'));
        $show->field('cost', __('Cost'));
        $show->field('fee', __('Fee'));
        $show->field('day_price', __('Day price'));
        $show->field('day_pledge', __('Day pledge'));
        $show->field('day_cost', __('Day cost'));
        $show->field('remark', __('Remark'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DefaultDayBonus());


        if ($form->isCreating()) {
            $form->select('product_id', __('Products'))->options(Product::all()->pluck('name', 'id'))->required();
        }
        if ($form->isEditing()) {
            $form->display('product.name', __('Products'));
        }
//        $form->number('product_id', __('Product id'));
        $form->decimal('power_add', __('Power add'))->default(0.00000);
        $form->decimal('coin_add', __('Coin add'))->default(0.00000);
        $form->decimal('efficiency', __('Efficiency'))->default(0.00000);
        $form->decimal('cost', __('Cost'))->default(0.00000);
        $form->decimal('fee', __('Fee'))->default(0.00000);
        $form->decimal('day_price', __('Day price'))->default(0.00000);
        $form->decimal('day_pledge', __('Day pledge'))->default(0.00000);
        $form->decimal('day_cost', __('Day cost'))->default(0.00000);
        $form->text('remark', __('Remark'));

        $form->tools(function (Form\Tools $tools) {
//            $tools->disableList(); // 去掉`列表`按钮
            $tools->disableDelete(); // 去掉`删除`按钮
            $tools->disableView(); // 去掉`查看`按钮
        });
        $form->footer(function ($footer) {
            $footer->disableReset();  // 去掉`重置`按钮
//            $footer->disableSubmit();   // 去掉`提交`按钮
            $footer->disableViewCheck(); // 去掉`查看`checkbox
            $footer->disableEditingCheck();  // 去掉`继续编辑`checkbox
            $footer->disableCreatingCheck();// 去掉`继续创建`checkbox
        });

        return $form;
    }
}
