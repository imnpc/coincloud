<?php

namespace App\Admin\Controllers;

use App\Models\ElectricCharge;
use App\Models\Product;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ElectricChargeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '电费';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ElectricCharge());

        $grid->column('id', __('Id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('product.wallet_slug', __('Wallet type id'));
        $grid->column('year', __('Year'));
        $grid->column('month', __('Month'));
        $grid->column('electric_charge', __('Electric charge'));
        $grid->column('number', __('Number'));
        $grid->column('total_fee', __('Total fee'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

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
        $show = new Show(ElectricCharge::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('year', __('Year'));
        $show->field('month', __('Month'));
        $show->field('electric_charge', __('Electric charge'));
        $show->field('number', __('Number'));
        $show->field('total_fee', __('Total fee'));
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
        $year = Carbon::now()->format('Y');
        $form = new Form(new ElectricCharge());
        if ($form->isCreating()) {
            $form->select('product_id', __('Product'))->options(Product::all()->pluck('name', 'id'))->required();
            $form->text('year', __('Year'))->default($year);
            $months = [
                1 => '一月',
                2 => '二月',
                3 => '三月',
                4 => '四月',
                5 => '五月',
                6 => '六月',
                7 => '七月',
                8 => '八月',
                9 => '九月',
                10 => '十月',
                11 => '十一月',
                12 => '十二月',
            ];

            $form->select('month', __('Month'))->options($months);
        }

        if ($form->isEditing()) {
            $form->display('product.name', __('Product'));
            $form->display('year', __('Year'));
            $form->display('month', __('Month'));
        }

        $form->decimal('electric_charge', __('Electric charge'));
//        $form->number('number', __('Number'));
//        $form->decimal('total_fee', __('Total fee'))->default(0.000000000000);
        $form->hidden('wallet_type_id', __('Wallet type id'));

        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
                $product = Product::find($form->product_id);
                $form->wallet_type_id = $product->wallet_type_id;
            });
        }

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
