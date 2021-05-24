<?php

namespace App\Admin\Controllers;

use App\Models\Withdraw;
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
    protected $title = 'Withdraw';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdraw());

        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('image', __('Image'));
        $grid->column('wallet_address', __('Wallet address'));
        $grid->column('coin', __('Coin'));
        $grid->column('fee', __('Fee'));
        $grid->column('real_coin', __('Real coin'));
        $grid->column('reason', __('Reason'));
        $grid->column('canceled_time', __('Canceled time'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));

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

        $form->number('user_id', __('User id'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->image('image', __('Image'));
        $form->text('wallet_address', __('Wallet address'));
        $form->decimal('coin', __('Coin'));
        $form->decimal('fee', __('Fee'));
        $form->decimal('real_coin', __('Real coin'));
        $form->text('reason', __('Reason'));
        $form->datetime('canceled_time', __('Canceled time'))->default(date('Y-m-d H:i:s'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
