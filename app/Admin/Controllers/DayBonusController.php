<?php

namespace App\Admin\Controllers;

use App\Models\DayBonus;
use App\Models\Order;
use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DayBonusController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '每日分红';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DayBonus());

        $grid->column('id', __('Id'));
//        $grid->column('product_id', __('Product id'));
        $grid->column('product.name', __('Products'));
        $grid->column('day', __('Day'));
        $grid->column('total_power', __('Total power'));
        $grid->column('power_add', __('Power add'));
        $grid->column('coin_add', __('Coin add'));
        $grid->column('efficiency', __('Efficiency'));
        $grid->column('cost', __('Cost'));
        $grid->column('fee', __('Fee'));
        $grid->column('day_price', __('Day price'));
        $grid->column('day_pledge', __('Day pledge'));
        $grid->column('day_cost', __('Day cost'));
//        $grid->column('remark', __('Remark'));
        $grid->column('status', __('Status'))->using([
            0 => '未执行',
            1 => '已执行',
        ], '未知')->label([
            0 => 'danger',
            1 => 'success',
        ], 'info');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
//            $actions->disableView();// 去掉查看
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
        $show = new Show(DayBonus::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('product_id', __('Product id'));
        $show->field('day', __('Day'));
        $show->field('total_power', __('Total power'));
        $show->field('power_add', __('Power add'));
        $show->field('coin_add', __('Coin add'));
        $show->field('efficiency', __('Efficiency'));
        $show->field('cost', __('Cost'));
        $show->field('fee', __('Fee'));
        $show->field('day_price', __('Day price'));
        $show->field('day_pledge', __('Day pledge'));
        $show->field('day_cost', __('Day cost'));
        $show->field('remark', __('Remark'));
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
        $form = new Form(new DayBonus());
        if ($form->isCreating()) {
            $form->select('product_id', __('Products'))->options(Product::all()->pluck('name', 'id'))->required();
        }
        if ($form->isEditing()) {
            $form->display('product.name', __('Products'));
        }
        $form->date('day', __('Day'))->default(date('Y-m-d'));
        $form->decimal('power_add', __('Power add'))->default(0.00000)->required()->help('算力增量');
        $form->decimal('coin_add', __('Coin add'))->default(0.00000)->required()->help('出块奖励');
        $form->decimal('efficiency', __('Efficiency'))->default(0.00000)->required()->help('挖矿效率');
        $form->decimal('cost', __('Cost'))->default(0.00000)->required()->help('抽查成本');
        $form->decimal('fee', __('Fee'))->default(0.00000)->required()->help('每 T 额外扣除,75%冻结部分扣除的');
        $form->decimal('day_price', __('Day price'))->default(0.00000)->required()->help('报单当天币价');
        $form->decimal('day_pledge', __('Day pledge'))->default(0.00000)->required()->help('报单当天质押币系数');
        $form->decimal('day_cost', __('Day cost'))->default(0.00000)->required()->help('报单当天单T有效算力封装成本');
        $form->text('remark', __('Remark'));
//        $form->switch('status', __('Status'));
        $form->hidden('total_power', __('Total power'));

        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
                $our_rate = config('system.our_rate');
                $total_power = Order::where('wait_status', '=', 0)
                    ->where('status', '=', 0)
                    ->where('pay_status', '=', 0)
                    ->where('product_id', '=', $form->product_id)
                    ->sum('number'); // 已经生效的云算力总数
                $form->total_power = $total_power; // 有效算力总数
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
