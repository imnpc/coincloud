<?php

namespace App\Admin\Controllers;

use App\Models\RechargeAccountLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RechargeAccountLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'RechargeAccountLog';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RechargeAccountLog());

        $grid->column('id', __('Id'));
        $grid->column('recharge_id', __('Recharge id'));
        $grid->column('user_id', __('User id'));
        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('day', __('Day'));
        $grid->column('power', __('Power'));
        $grid->column('day_pledge', __('Day pledge'));
        $grid->column('day_gas', __('Day gas'));
        $grid->column('pledge', __('Pledge'));
        $grid->column('gas', __('Gas'));
        $grid->column('total', __('Total'));
        $grid->column('used', __('Used'));
        $grid->column('day_limit', __('Day limit'));
        $grid->column('remark', __('Remark'));
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
        $show = new Show(RechargeAccountLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('recharge_id', __('Recharge id'));
        $show->field('user_id', __('User id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('day', __('Day'));
        $show->field('power', __('Power'));
        $show->field('day_pledge', __('Day pledge'));
        $show->field('day_gas', __('Day gas'));
        $show->field('pledge', __('Pledge'));
        $show->field('gas', __('Gas'));
        $show->field('total', __('Total'));
        $show->field('used', __('Used'));
        $show->field('day_limit', __('Day limit'));
        $show->field('remark', __('Remark'));
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
        $form = new Form(new RechargeAccountLog());

        $form->number('recharge_id', __('Recharge id'));
        $form->number('user_id', __('User id'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('power', __('Power'));
        $form->decimal('day_pledge', __('Day pledge'));
        $form->decimal('day_gas', __('Day gas'));
        $form->decimal('pledge', __('Pledge'));
        $form->decimal('gas', __('Gas'));
        $form->decimal('total', __('Total'));
        $form->decimal('used', __('Used'));
        $form->decimal('day_limit', __('Day limit'));
        $form->text('remark', __('Remark'));

        return $form;
    }
}
