<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class Pro2Controller extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('tag', __('Tag'));
        $grid->column('price', __('Price'));
        $grid->column('price_usdt', __('Price usdt'));
        $grid->column('price_coin', __('Price coin'));
        $grid->column('coin_wallet_address', __('Coin wallet address'));
        $grid->column('coin_wallet_qrcode', __('Coin wallet qrcode'));
        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('wait_days', __('Wait days'));
        $grid->column('valid_days', __('Valid days'));
        $grid->column('valid_days_text', __('Valid days text'));
        $grid->column('choose_reason', __('Choose reason'));
        $grid->column('choose_reason_text', __('Choose reason text'));
        $grid->column('service_rate', __('Service rate'));
        $grid->column('pay_user_rate', __('Pay user rate'));
        $grid->column('now_rate', __('Now rate'));
        $grid->column('freed_rate', __('Freed rate'));
        $grid->column('freed_days', __('Freed days'));
        $grid->column('parent1_rate', __('Parent1 rate'));
        $grid->column('parent2_rate', __('Parent2 rate'));
        $grid->column('invite_rate', __('Invite rate'));
        $grid->column('bonus_team_a', __('Bonus team a'));
        $grid->column('bonus_team_b', __('Bonus team b'));
        $grid->column('bonus_team_c', __('Bonus team c'));
        $grid->column('upgrade_team_a', __('Upgrade team a'));
        $grid->column('upgrade_team_b', __('Upgrade team b'));
        $grid->column('upgrade_team_c', __('Upgrade team c'));
        $grid->column('risk_rate', __('Risk rate'));
        $grid->column('gas_fee', __('Gas fee'));
        $grid->column('pledge_fee', __('Pledge fee'));
        $grid->column('pledge_days', __('Pledge days'));
        $grid->column('valid_rate', __('Valid rate'));
        $grid->column('package_rate', __('Package rate'));
        $grid->column('thumb', __('Thumb'));
        $grid->column('desc', __('Desc'));
        $grid->column('content', __('Content'));
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));
        $grid->column('total_revenue', __('Total revenue'));
        $grid->column('yesterday_revenue', __('Yesterday revenue'));
        $grid->column('yesterday_gas', __('Yesterday gas'));
        $grid->column('yesterday_efficiency', __('Yesterday efficiency'));
        $grid->column('total_revenue_text', __('Total revenue text'));
        $grid->column('yesterday_revenue_text', __('Yesterday revenue text'));
        $grid->column('yesterday_gas_text', __('Yesterday gas text'));
        $grid->column('yesterday_efficiency_text', __('Yesterday efficiency text'));

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('tag', __('Tag'));
        $show->field('price', __('Price'));
        $show->field('price_usdt', __('Price usdt'));
        $show->field('price_coin', __('Price coin'));
        $show->field('coin_wallet_address', __('Coin wallet address'));
        $show->field('coin_wallet_qrcode', __('Coin wallet qrcode'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('wait_days', __('Wait days'));
        $show->field('valid_days', __('Valid days'));
        $show->field('valid_days_text', __('Valid days text'));
        $show->field('choose_reason', __('Choose reason'));
        $show->field('choose_reason_text', __('Choose reason text'));
        $show->field('service_rate', __('Service rate'));
        $show->field('pay_user_rate', __('Pay user rate'));
        $show->field('now_rate', __('Now rate'));
        $show->field('freed_rate', __('Freed rate'));
        $show->field('freed_days', __('Freed days'));
        $show->field('parent1_rate', __('Parent1 rate'));
        $show->field('parent2_rate', __('Parent2 rate'));
        $show->field('invite_rate', __('Invite rate'));
        $show->field('bonus_team_a', __('Bonus team a'));
        $show->field('bonus_team_b', __('Bonus team b'));
        $show->field('bonus_team_c', __('Bonus team c'));
        $show->field('upgrade_team_a', __('Upgrade team a'));
        $show->field('upgrade_team_b', __('Upgrade team b'));
        $show->field('upgrade_team_c', __('Upgrade team c'));
        $show->field('risk_rate', __('Risk rate'));
        $show->field('gas_fee', __('Gas fee'));
        $show->field('pledge_fee', __('Pledge fee'));
        $show->field('pledge_days', __('Pledge days'));
        $show->field('valid_rate', __('Valid rate'));
        $show->field('package_rate', __('Package rate'));
        $show->field('thumb', __('Thumb'));
        $show->field('desc', __('Desc'));
        $show->field('content', __('Content'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));
        $show->field('total_revenue', __('Total revenue'));
        $show->field('yesterday_revenue', __('Yesterday revenue'));
        $show->field('yesterday_gas', __('Yesterday gas'));
        $show->field('yesterday_efficiency', __('Yesterday efficiency'));
        $show->field('total_revenue_text', __('Total revenue text'));
        $show->field('yesterday_revenue_text', __('Yesterday revenue text'));
        $show->field('yesterday_gas_text', __('Yesterday gas text'));
        $show->field('yesterday_efficiency_text', __('Yesterday efficiency text'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('name', __('Name'));
        $form->text('tag', __('Tag'));
        $form->decimal('price', __('Price'))->default(0.00);
        $form->decimal('price_usdt', __('Price usdt'))->default(0.00);
        $form->decimal('price_coin', __('Price coin'))->default(0.00);
        $form->text('coin_wallet_address', __('Coin wallet address'));
        $form->text('coin_wallet_qrcode', __('Coin wallet qrcode'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->number('wait_days', __('Wait days'));
        $form->number('valid_days', __('Valid days'));
        $form->text('valid_days_text', __('Valid days text'));
        $form->text('choose_reason', __('Choose reason'));
        $form->text('choose_reason_text', __('Choose reason text'));
        $form->decimal('service_rate', __('Service rate'))->default(0.00);
        $form->decimal('pay_user_rate', __('Pay user rate'))->default(0.00);
        $form->decimal('now_rate', __('Now rate'))->default(0.00);
        $form->decimal('freed_rate', __('Freed rate'))->default(0.00);
        $form->number('freed_days', __('Freed days'));
        $form->decimal('parent1_rate', __('Parent1 rate'))->default(0.00);
        $form->decimal('parent2_rate', __('Parent2 rate'))->default(0.00);
        $form->decimal('invite_rate', __('Invite rate'))->default(0.00);
        $form->decimal('bonus_team_a', __('Bonus team a'))->default(0.00);
        $form->decimal('bonus_team_b', __('Bonus team b'))->default(0.00);
        $form->decimal('bonus_team_c', __('Bonus team c'))->default(0.00);
        $form->number('upgrade_team_a', __('Upgrade team a'));
        $form->number('upgrade_team_b', __('Upgrade team b'));
        $form->number('upgrade_team_c', __('Upgrade team c'));
        $form->decimal('risk_rate', __('Risk rate'))->default(0.00);
        $form->decimal('gas_fee', __('Gas fee'))->default(0.00000);
        $form->decimal('pledge_fee', __('Pledge fee'))->default(0.00000);
        $form->number('pledge_days', __('Pledge days'))->default(1);
        $form->decimal('valid_rate', __('Valid rate'))->default(0.00);
        $form->decimal('package_rate', __('Package rate'))->default(0.00);
        $form->text('thumb', __('Thumb'));
        $form->text('desc', __('Desc'));
        $form->textarea('content', __('Content'));
        $form->switch('status', __('Status'))->default(1);
        $form->decimal('total_revenue', __('Total revenue'))->default(0.00000);
        $form->decimal('yesterday_revenue', __('Yesterday revenue'))->default(0.00000);
        $form->decimal('yesterday_gas', __('Yesterday gas'))->default(0.00000);
        $form->decimal('yesterday_efficiency', __('Yesterday efficiency'))->default(0.00000);
        $form->text('total_revenue_text', __('Total revenue text'))->default('矿池总产量');
        $form->text('yesterday_revenue_text', __('Yesterday revenue text'))->default('昨日产量');
        $form->text('yesterday_gas_text', __('Yesterday gas text'))->default('昨日消耗GAS');
        $form->text('yesterday_efficiency_text', __('Yesterday efficiency text'))->default('昨日挖矿效率');

        return $form;
    }
}
