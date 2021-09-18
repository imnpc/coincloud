<?php

namespace App\Admin\Controllers;

use App\Jobs\AutoCreatePledge;
use App\Models\Recharge;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RechargeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '充值';

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
        $grid = new Grid(new Recharge());

        $grid->column('id', __('Id'));
        $grid->column('order_sn', __('Order sn'));
        $grid->column('user_id', __('User id'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('user.mobile', __('Mobile'));
        $grid->column('wallet_slug', __('Wallet slug'));
        $grid->column('product.name', __('Products'));
        $grid->column('coin', __('Coin'));
        $grid->column('used_coin', __('Used coin'));
        $grid->column('pledge_fee', __('Pledge'));
        $grid->column('gas_fee', __('Gas fee'));
//        $grid->column('pay_type', __('Pay type'))->using(['1' => '充值', '2' => '账户转入', '3' => '公司代充值']);
//        $grid->column('pay_image', __('Pay image'));
        $grid->column('pay_time', __('Pay time'));
        $grid->column('confirm_time', __('Confirm time'));
        // 支付状态 0-未提交 1-审核中 2-已完成 success
        $grid->column('pay_status', __('Pay status'))->using([
            -1 => '已取消',
            0 => '未提交',
            1 => '审核中',
            2 => '已完成',
        ], '未知')->label([
            -1 => 'danger',
            0 => 'danger',
            1 => 'warning',
            2 => 'success',
        ], 'info');
        //排单状态 0-排单中 1-已排单 2-已略过
        $grid->column('schedule', __('Schedule'))->using([
            0 => '排单中',
            1 => '已排单',
            2 => '已略过',
        ], '未知')->label([
            0 => 'default',
            1 => 'success',
            2 => 'warning',
        ], 'info');
//        $grid->column('schedule_time', __('Schedule time'));
//        $grid->column('finished_time', __('Finished time'));
        $grid->column('is_return', __('是否退回'))->using([
            0 => '无',
            1 => '已退回',
        ], '未知')->label([
            0 => 'default',
            1 => 'warning',
        ], 'info');
        $grid->column('return_coin', __('Return coin'));
//        $grid->column('reason', __('Reason'));
//        $grid->column('remark', __('Remark'));
//        $grid->column('canceled_time', __('Canceled time'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->disableCreateButton(); // 禁用创建按钮
        //$grid->disableActions(); // 禁用行操作列

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
            //$actions->disableEdit();// 去掉编辑
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
        $show = new Show(Recharge::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_sn', __('Order sn'));
        $show->field('user_id', __('User id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('coin', __('Coin'));
        $show->field('used_coin', __('Used coin'));
        $show->field('pledge_fee', __('Pledge'));
        $show->field('gas_fee', __('Gas fee'));
        $show->field('pay_type', __('Pay type'));
        $show->field('pay_image', __('Pay image'));
        $show->field('pay_time', __('Pay time'));
        $show->field('confirm_time', __('Confirm time'));
        $show->field('pay_status', __('Pay status'));
        $show->field('schedule', __('Schedule'));
        $show->field('schedule_time', __('Schedule time'));
        $show->field('finished_time', __('Finished time'));
        $show->field('is_return', __('Is return'));
        $show->field('return_coin', __('Return coin'));
        $show->field('reason', __('Reason'));
        $show->field('remark', __('Remark'));
        $show->field('canceled_time', __('Canceled time'));
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
        $form = new Form(new Recharge());

        $form->text('order_sn', __('Order sn'));
//        $form->number('user_id', __('User id'));
        $form->display('user.mobile', __('Mobile'));
//        $form->number('wallet_type_id', __('Wallet type id'));
        $form->display('wallet_slug', __('Wallet slug'));
        $form->display('product.name', __('Products'));
        $form->display('coin', __('Coin'));
        $form->display('pay_type', __('Pay type'))->with(function ($value) {
            return Recharge::$paymentMap[$value];
        });
//        $form->text('pay_image', __('Pay image'));
        $form->display('pay_image_url', __('支付凭证图片'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->display('pay_time', __('Pay time'));
        $form->display('confirm_time', __('Confirm time'));
//        $form->switch('pay_status', __('Pay status'));
        $form->display('created_at', __('Created at'));
        $form->hidden('user_id', __('User id'));

//        $form->text('reason', __('Reason'));
//        $form->text('remark', __('Remark'));
//        $form->datetime('canceled_time', __('Canceled time'))->default(date('Y-m-d H:i:s'));

        if ($form->isEditing()) {
            // 支付状态 0-未提交 1-审核中 2-已完成
            $id = request()->route('recharge');
            $check = Recharge::find($id);
            if ($check->pay_status >= 0 && $check->pay_status < 2) {
                $form->radioCard('pay_status', __('Pay status'))
                    ->options(['0' => '未提交', '1' => '审核中', '2' => '已完成', '-1' => '取消充值'])
                    ->when('-1', function (Form $form) {
                        $form->text('reason', '取消原因')->placeholder('请务必填写取消理由');
                    })
                    ->default('0')->required();
            } elseif ($check->status == '2') {
                $form->display('pay_status', __('Pay status'))->with(function ($value) {
                    return "<span class='label label-success'>已完成</span>";
                });
            } elseif ($check->status == '-1') {
                $form->display('status', __('Status'))->with(function ($value) {
                    return "<span class='label label-danger'>已取消</span>";
                });
                $form->display('reason', __('取消原因'));
            } else {
                $form->display('pay_status', __('Pay status'))->with(function ($value) {
                    return Recharge::$statusMap[$value];
                });
            }

            $form->saving(function (Form $form) {
                //填写获取表单内容
                $this->beforePayStatus = $form->model()->pay_status;
            });
            $form->saved(function (Form $form) {
                //添加要判断及更改的字段
                if ($this->beforePayStatus != $form->model()->pay_status && $form->model()->pay_status == 2) {
                    $day = Carbon::now()->toDateString();
                    $order = Recharge::find($form->model()->id);
                    $order->confirm_time = Carbon::now(); // 标记订单确认时间
                    $order->save();
                    // 派发到计划任务执行 $form->model()->id  TODO
                    AutoCreatePledge::dispatch($form->model()->id);
                }
            });

        }

        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();  // 去掉`列表`按钮
            $tools->disableDelete();  // 去掉`删除`按钮
            $tools->disableView();  // 去掉`查看`按钮
        });

        return $form;
    }
}
