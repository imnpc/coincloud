<?php

namespace App\Admin\Controllers;

use App\Models\SystemWallet;
use App\Models\SystemWalletLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;

class SystemWalletLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '系统钱包';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SystemWalletLog());

        $grid->header(function ($query) {

            $wallet = SystemWallet::all();
            foreach ($wallet as $k => $v){
                $infoBox = new InfoBox($v->product->name . '-服务费', 'dollar', 'aqua', '', $v->service_fee);
                $infoBox = $infoBox->render();
                return "
<div class='col-md-3'>$infoBox</div>
";
            }
        });

        $grid->column('id', __('Id'));
        $grid->column('system_wallet_id', __('System wallet id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Product'));
//        $grid->column('wallet_type_id', __('Wallet type id'));
        $grid->column('wallet_slug', __('Wallet type id'));
        $grid->column('day', __('Day'));
//        $grid->column('old_team_a', __('Old team a'));
//        $grid->column('old_team_b', __('Old team b'));
//        $grid->column('old_team_c', __('Old team c'));
//        $grid->column('old_risk', __('Old risk'));
//        $grid->column('old_commission_balance', __('Old commission balance'));
        $grid->column('old_service_fee', __('Old service fee'));
//        $grid->column('team_a_add', __('Team a add'));
//        $grid->column('team_b_add', __('Team b add'));
//        $grid->column('team_c_add', __('Team c add'));
//        $grid->column('risk_add', __('Risk add'));
//        $grid->column('commission_balance_add', __('Commission balance add'));
        $grid->column('service_fee_add', __('Service fee add'));
//        $grid->column('team_a', __('Team a'));
//        $grid->column('team_b', __('Team b'));
//        $grid->column('team_c', __('Team c'));
//        $grid->column('risk', __('Risk'));
//        $grid->column('commission_balance', __('Commission balance'));
        $grid->column('service_fee', __('Service fee'));
        $grid->column('from_user_id', __('From user id'));
        $grid->column('order_id', __('Order id'));
        $grid->column('remark', __('Remark'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->disableColumnSelector();// 禁用行选择器
        $grid->disableCreateButton(); // 禁用创建按钮
        $grid->disableActions(); // 禁用行操作列
        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
            $actions->disableEdit();// 去掉编辑
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
        $show = new Show(SystemWalletLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('system_wallet_id', __('System wallet id'));
        $show->field('product_id', __('Product id'));
        $show->field('wallet_type_id', __('Wallet type id'));
        $show->field('day', __('Day'));
        $show->field('old_team_a', __('Old team a'));
        $show->field('old_team_b', __('Old team b'));
        $show->field('old_team_c', __('Old team c'));
        $show->field('old_risk', __('Old risk'));
        $show->field('old_commission_balance', __('Old commission balance'));
        $show->field('old_service_fee', __('Old service fee'));
        $show->field('team_a_add', __('Team a add'));
        $show->field('team_b_add', __('Team b add'));
        $show->field('team_c_add', __('Team c add'));
        $show->field('risk_add', __('Risk add'));
        $show->field('commission_balance_add', __('Commission balance add'));
        $show->field('service_fee_add', __('Service fee add'));
        $show->field('team_a', __('Team a'));
        $show->field('team_b', __('Team b'));
        $show->field('team_c', __('Team c'));
        $show->field('risk', __('Risk'));
        $show->field('commission_balance', __('Commission balance'));
        $show->field('service_fee', __('Service fee'));
        $show->field('from_user_id', __('From user id'));
        $show->field('order_id', __('Order id'));
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
        $form = new Form(new SystemWalletLog());

        $form->number('system_wallet_id', __('System wallet id'));
        $form->number('product_id', __('Product id'));
        $form->number('wallet_type_id', __('Wallet type id'));
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('old_team_a', __('Old team a'))->default(0.00000);
        $form->decimal('old_team_b', __('Old team b'))->default(0.00000);
        $form->decimal('old_team_c', __('Old team c'))->default(0.00000);
        $form->decimal('old_risk', __('Old risk'))->default(0.00000);
        $form->decimal('old_commission_balance', __('Old commission balance'))->default(0.00000);
        $form->decimal('old_service_fee', __('Old service fee'))->default(0.00000);
        $form->decimal('team_a_add', __('Team a add'));
        $form->decimal('team_b_add', __('Team b add'));
        $form->decimal('team_c_add', __('Team c add'));
        $form->decimal('risk_add', __('Risk add'));
        $form->decimal('commission_balance_add', __('Commission balance add'));
        $form->decimal('service_fee_add', __('Service fee add'));
        $form->decimal('team_a', __('Team a'));
        $form->decimal('team_b', __('Team b'));
        $form->decimal('team_c', __('Team c'));
        $form->decimal('risk', __('Risk'));
        $form->decimal('commission_balance', __('Commission balance'));
        $form->decimal('service_fee', __('Service fee'));
        $form->number('from_user_id', __('From user id'));
        $form->number('order_id', __('Order id'));
        $form->text('remark', __('Remark'));

        return $form;
    }
}
