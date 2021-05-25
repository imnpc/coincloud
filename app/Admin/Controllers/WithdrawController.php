<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\UserWalletLog;
use App\Models\Withdraw;
use App\Services\LogService;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WithdrawController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '提币';

    /**
     * 用来记录订单支付状态变化
     */
    private $beforeStatus;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdraw());

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
        });

        $grid->column('id', __('Id'));
//        $grid->column('user_id', __('User id'));
        $grid->column('user.mobile', __('用户'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('wallet_slug', __('Wallet slug'));
//        $grid->column('image', __('Image'));
        $grid->column('wallet_address', __('Wallet address'));
        $grid->column('coin', __('Coin'));
        $grid->column('fee', __('Fee'));
        $grid->column('real_coin', __('Real coin'))->display(function ($value) {
            return "<span class='label label-danger'>$value</span>";
        });
        $grid->column('reason', __('Reason'));
        $grid->column('canceled_time', __('Canceled time'));
        //0-审核中 1-已完成 2-已取消
        $grid->column('status', __('Status'))->using([
            0 => '审核中',
            1 => '已完成',
            2 => '已取消',
        ], '未知')->label([
            0 => 'danger',
            1 => 'success',
            2 => 'warning',
        ], 'info');
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
        $show = new Show(Withdraw::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('image', __('Image'));
        $show->field('wallet_address', __('Wallet address'));
        $show->field('coin', __('Coin'));
        $show->field('fee', __('Fee'));
        $show->field('real_coin', __('Real coin'));
        $show->field('reason', __('Reason'));
        $show->field('canceled_time', __('Canceled time'));
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
        $form = new Form(new Withdraw());

//        $form->number('user_id', __('User id'));
        $form->display('user.mobile', __('User id'));
//        $form->number('wallet_type_id', __('Wallet type id'));
        $form->display('wallet_slug', __('Wallet slug'));
//        $form->image('image', __('Image'));
        $form->display('image_url', __('钱包图片'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->display('wallet_address', __('Wallet address'));
        $form->display('coin', __('Coin'));
        $form->display('fee', __('Fee'));
        $form->display('real_coin', __('Real coin'))->with(function ($value) {
            return "<span class='label label-danger'>$value</span>";
        });
//        $form->text('reason', __('Reason'));
//        $form->datetime('canceled_time', __('Canceled time'))->default(date('Y-m-d H:i:s'));
//        $form->switch('status', __('Status'));

        $form->display('created_at', __('Created at'));
        //0-审核中 1-已完成 2-已取消
        if ($form->isEditing()) {
            $id = request()->route('withdraw');
            $check = Withdraw::find($id);
            if ($check->status == '0') {
                $form->radioCard('status', __('Status'))
                    ->options(['0' => '审核中', '1' => '已完成', '2' => '取消申请'])
                    ->when(2, function (Form $form) {
                        $form->text('reason', '取消原因')->placeholder('请务必填写取消理由');
                    })
                    ->default('0')->required();
            } elseif ($check->status == '1') {
                $form->display('status', __('Status'))->with(function ($value) {
                    return "<span class='label label-success'>已完成</span>";
                });
            } elseif ($check->status == '2') {
                $form->display('status', __('Status'))->with(function ($value) {
                    return "<span class='label label-danger'>已取消</span>";
                });
                $form->display('reason', __('取消原因'));
            } else {
                $form->display('status', __('Status'));
            }

            $form->saving(function (Form $form) {
                //填写获取表单内容
                $this->beforeStatus = $form->model()->status;
            });
            // 取消的退回用户余额 TODO
            $form->saved(function (Form $form) {
                //添加要判断及更改的字段
                if ($this->beforeStatus != $form->model()->status && $form->model()->status == 2) {
                    $day = Carbon::now()->toDateString();
                    $order = Withdraw::find($form->model()->id);
                    $order->canceled_time = Carbon::now(); // 标记订单确认时间
                    $order->save();
                    // 给用户账户增加对应金额
                    $logService = app()->make(LogService::class);
                    if ($form->model()->status == 2) {
                        // 充值账户转入 TODO
                        $remark2 = "取消提币退回 " . $form->model()->coin . ',理由-' . $form->model()->reason . '#' . $form->model()->id;
                        $logService->userLog(User::BALANCE_FILECOIN, $form->model()->user_id, $form->model()->coin, 0, $day, UserWalletLog::FROM_CANCEL_WITHDRAW, $remark2, 0, 0, 0, UserWalletLog::TYPE_MINER);
                    }
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
