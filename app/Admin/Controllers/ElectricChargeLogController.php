<?php

namespace App\Admin\Controllers;

use App\Models\ElectricChargeLog;
use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Model;

class ElectricChargeLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '电费记录';

    /**
     * 用来记录订单支付状态变化
     */
    private $beforePayStatus;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ElectricChargeLog());

        $grid->column('id', __('Id'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user.name', __('Name'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('electric_charge_id', __('Electric charge id'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('product.wallet_slug', __('Wallet type id'));
        $grid->column('year', __('Year'));
        $grid->column('month', __('Month'));
        $grid->column('electric_charge', __('Electric charge'));
        $grid->column('number', __('Number'));
        $grid->column('total_fee', __('Total fee'));
//        $grid->column('pay_image', __('Pay image'));
        $grid->column('pay_time', __('Pay time'));
        $grid->column('confirm_time', __('Confirm time'));
        $grid->column('pay_status', __('Pay status'))->using([
            0 => '已完成',
            1 => '未提交',
            2 => '审核中',
        ], '未知')->label([
            0 => 'success',
            1 => 'default',
            2 => 'danger',
        ], 'warning'); // 支付状态 0-已完成 1-未提交 2-审核中
//        $grid->column('pay_status', __('Pay status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->disableCreateButton(); // 禁用创建按钮
//        $grid->disableActions(); // 禁用行操作列

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
        $show = new Show(ElectricChargeLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('electric_charge_id', __('Electric charge id'));
        $show->field('product_id', __('Product id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('year', __('Year'));
        $show->field('month', __('Month'));
        $show->field('electric_charge', __('Electric charge'));
        $show->field('number', __('Number'));
        $show->field('total_fee', __('Total fee'));
        $show->field('pay_image', __('Pay image'));
        $show->field('pay_time', __('Pay time'));
        $show->field('confirm_time', __('Confirm time'));
        $show->field('pay_status', __('Pay status'));
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
        $form = new Form(new ElectricChargeLog());

//        $form->number('user_id', __('User id'));
        $form->display('user.name', __('Name'));
        $form->display('product.name', __('Products'));
        $form->display('electric_charge_id', __('Electric charge id'));
//        $form->number('product_id', __('Product id'));
//        $form->number('wallet_type_id', __('Wallet type id'));
        $form->display('product.wallet_slug', __('Wallet type id'));
        $form->display('year', __('Year'));
        $form->display('month', __('Month'));
        $form->display('electric_charge', __('Electric charge'));
        $form->display('number', __('Number'));
        $form->display('total_fee', __('Total fee'))->default(0.000000000000);
//        $form->text('pay_image', __('Pay image'));
        $form->display('pay_image_url', __('支付凭证图片'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->display('pay_time', __('Pay time'))->default(date('Y-m-d H:i:s'));
        $form->display('confirm_time', __('Confirm time'))->default(date('Y-m-d H:i:s'));
//        $form->switch('pay_status', __('Pay status'));

        // 支付状态 0-已完成 1-未提交 2-审核中
        if ($form->isEditing()) {
            $id = request()->route('electriclog');
            $check = ElectricChargeLog::find($id);
            if ($check->pay_status > 0) {
                $form->radioCard('pay_status', __('Pay status'))->options(['0' => '已完成', '1' => '未提交', '2' => '审核中'])->default('2')->required();
            } elseif ($check->status == '0') {
                $form->display('pay_status', __('Pay status'))->with(function ($value) {
                    return "<span class='label label-success'>已完成</span>";
                });
            } else {
                $form->display('pay_status', __('Pay status'))->with(function ($value) {
                    return Order::$paidMap[$value];
                });
            }

            $form->saving(function (Form $form) {
                $this->beforePayStatus = $form->model()->pay_status;
            });

            $form->saved(function (Form $form) {
                if ($this->beforePayStatus != $form->model()->pay_status && $form->model()->pay_status == 0) {
                    $day = Carbon::now()->toDateString();
                    $order = ElectricChargeLog::find($form->model()->id);
                    $order->confirm_time = Carbon::now(); // 标记订单确认时间
                    $order->save();
                }
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
