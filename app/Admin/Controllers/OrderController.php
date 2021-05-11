<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

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
        $grid = new Grid(new Order());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('id', __('Id'));
            $filter->equal('order_sn', __('Order sn'));
            $filter->equal('user_id', __('Mobile'))->select(User::all()->pluck('mobile', 'id'));
            $filter->equal('product_id', __('Products'))->select(Product::all()->pluck('name', 'id'));
            $filter->equal('package_status', __('Package status'))->radio([
                '' => '所有',
                0 => '封装完成',
                1 => '等待封装',
                2 => '封装中'
            ]); // 封装状态 0-封装完成 1-等待封装 2-封装中
            $filter->equal('pay_status', __('Pay status'))->radio([
                '' => '所有',
                0 => '已完成',
                1 => '未提交',
                2 => '审核中',
            ]); // 支付状态 0-已完成 1-未提交 2-审核中
            $filter->equal('status', __('Status'))->radio([
                '' => '所有',
                0 => '有效',
                1 => '无效',
            ]); // 订单状态 0-有效 1-无效
            $filter->between('created_at', __('Created at'))->datetime();
        });

        $grid->column('id', __('Id'));
        $grid->column('order_sn', __('Order sn'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user.mobile', __('Mobile'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('number', __('Number'));
        $grid->column('pay_money', __('Pay money'))->display(function ($value) {
            $ext = '';
            if ($this->payment == 0 || $this->payment == 1) {
                $ext = '<span class="text-primary">￥</span>';
            } else {
                $ext = '<span class="text-red">' . $this->payment_type . '</span>';
            }
            return $value . ' ' . $ext;
        });// 支付方式 1-银行转账 2-USDT 3-其他虚拟币
//        $grid->column('wait_days', __('Wait days'));
//        $grid->column('wait_status', __('Wait status'));
        $grid->column('valid_days', __('Valid days'));
        $grid->column('valid_rate', __('Valid rate'));
        $grid->column('valid_power', __('Valid power'));
        $grid->column('max_valid_power', __('Max valid power'));
//        $grid->column('package_rate', __('Package rate'));
//        $grid->column('package_already', __('Package already'));
//        $grid->column('package_wait', __('Package wait'));
//        $grid->column('package_status', __('Package status'));
//        $grid->column('payment', __('Payment'));
//        $grid->column('payment_type', __('Payment type'));
        $grid->column('pay_status', __('Pay status'))->using([
            0 => '已完成',
            1 => '未提交',
            2 => '审核中',
        ], '未知')->label([
            0 => 'success',
            1 => 'default',
            2 => 'danger',
        ], 'warning'); // 支付状态 0-已完成 1-未提交 2-审核中
//        $grid->column('pay_image', __('Pay image'));
        $grid->column('pay_time', __('Pay time'));
        $grid->column('confirm_time', __('Confirm time'));
//        $grid->column('is_output_coin', __('Is output coin'));
//        $grid->column('remark', __('Remark'));
        $grid->column('status', __('Status'))->using([
            0 => '有效',
            1 => '无效',
        ], '未知')->label([
            0 => 'success',
            1 => 'danger',
        ], 'warning'); // 订单状态 0-有效 1-无效
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
//        if (!Admin::user()->isAdministrator()) {
//            $grid->disableCreateButton(); // 禁用创建按钮
//        }
        $grid->disableCreateButton(); // 禁用创建按钮

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->actions(function ($actions) {
            if ($actions->row['pay_status'] == 2) {
                $actions->disableDelete();// 去掉删除
            }
            //$actions->disableDelete();// 去掉删除
            //$actions->disableView();// 去掉查看
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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_sn', __('Order sn'));
        $show->field('user_id', __('User id'));
        $show->field('user_id', __('Users'))->as(function ($user_id) {
            return User::find($user_id)->mobile;
        });
        $show->field('product_id', __('Products'))->as(function ($product_id) {
            return Product::find($product_id)->name;
        });
//        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('number', __('Number'))->label('warning');
        $show->field('pay_money', __('Pay money'))->label();
        $show->field('wait_days', __('Wait days'));
        $show->field('wait_status', __('Wait status'))->using(['0' => '已生效', '1' => '等待中']);
        $show->field('valid_days', __('Valid days'));
        $show->field('valid_rate', __('Valid rate'));
        $show->field('valid_power', __('Valid power'));
        $show->field('max_valid_power', __('Max valid power'));
        $show->field('package_rate', __('Package rate'));
        $show->field('package_already', __('Package already'));
        $show->field('package_wait', __('Package wait'));
        $show->field('package_status', __('Package status'))->using(['0' => '封装完成', '1' => '等待封装', '2' => '封装中']);
        $show->field('payment', __('Payment'))->using(['0' => '后台', '1' => '银行转账', '2' => 'USDT', '3' => '其他虚拟币']);
        $show->field('payment_type', __('Payment type'));
        $show->field('pay_status', __('Pay status'))->using(['0' => '已完成', '1' => '未提交', '2' => '审核中'])->label('danger');
        $show->field('pay_image', __('Pay image'))->image();
        //$show->avatar()->image();
        $show->field('pay_time', __('Pay time'));
        $show->field('confirm_time', __('Confirm time'));
//        $show->field('is_output_coin', __('Is output coin'));
        $show->field('remark', __('Remark'));
        $show->field('status', __('Status'))->using(['0' => '有效', '1' => '无效']);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
//        $show->field('deleted_at', __('Deleted at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableEdit();
            //$tools->disableList();
            $tools->disableDelete();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());

        $form->display('order_sn', __('Order sn'));
        $form->display('user.mobile', __('Users'));

        $form->display('product.name', __('Products'));
//        $form->display('wallet_type_id', __('Wallet type id'));
        $form->display('number', __('Number'));
        $form->display('pay_money', __('Pay money'))->with(function ($value) {
            $ext = '';
            if ($this->payment == 0 || $this->payment == 1) {
                $ext = '<span class="text-primary"> ￥ </span>';
            } else {
                $ext = '<span class="text-red"> ' . $this->payment_type . ' </span>';
            }
            return $value . $ext;
        });// 支付方式 1-银行转账 2-USDT 3-其他虚拟币;
        $form->display('wait_days', __('Wait days'));
        $form->display('wait_status', __('Wait status'))->with(function ($value) {
            return Order::$waitMap[$value];
        });
        $form->display('valid_days', __('Valid days'));
        $form->display('valid_rate', __('Valid rate'))->default(0.00);
        $form->display('valid_power', __('Valid power'));
        $form->display('max_valid_power', __('Max valid power'));
        $form->display('package_rate', __('Package rate'))->default(0.00);
        $form->display('package_already', __('Package already'));
        $form->display('package_wait', __('Package wait'));
        $form->display('package_status', __('Package status'))->with(function ($value) {
            return Order::$packageMap[$value];
        });
        $form->display('payment', __('Payment'))->with(function ($value) {
            return Order::$paymentMap[$value];
        });
        $form->display('payment_type', __('Payment type'));

//        $form->text('pay_image', __('Pay image'));
        $form->display('pay_image_url', __('支付凭证图片'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->display('pay_time', __('Pay time'));
        $form->display('confirm_time', __('Confirm time'));
//        $form->switch('is_output_coin', __('Is output coin'));
        $form->text('remark', __('Remark'));
//        $form->switch('pay_status', __('Pay status'));
//        $form->switch('status', __('Status'));
        $form->radioCard('status', __('Status'))->options(['0' => '有效', '1' => '无效'])->default('0');
        // 支付状态 0-已完成 1-未提交 2-审核中
        if ($form->isEditing()) {
            $id = request()->route('order');
            $check = Order::find($id);
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
                    $order = Order::find($form->model()->id);
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
