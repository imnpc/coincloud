<?php

namespace App\Admin\Controllers;

use App\Models\WalletType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WalletTypeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '钱包类型';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WalletType());
        $grid->sortable();

        $grid->column('id', __('Id'));
        $grid->column('icon_url', __('Icon'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('name', __('Name'));
        $grid->column('slug', __('Slug'));
        $grid->column('description', __('Description'));
        $grid->column('decimal_places', __('Decimal places'));
        //$grid->column('is_enblened', __('Is enblened'));
        // 设置text、color、和存储值
        $states = [
            'on' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
        ];
        $grid->column('is_enblened', __('Is enblened'))->switch($states);
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

        $grid->disableFilter(); // 禁用查询过滤器
        $grid->disableRowSelector(); // 禁用行选择checkbox
//        $grid->disableCreateButton(); // 禁用创建按钮
//        $grid->disableActions(); // 禁用行操作列
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
        $show = new Show(WalletType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('slug', __('Slug'));
        $show->field('description', __('Description'));
        $show->field('decimal_places', __('Decimal places'));
        $show->field('is_enblened', __('Is enblened'));
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
        $form = new Form(new WalletType());

        if ($form->isCreating()) {
            $form->text('name', __('Name'))->required()->help('钱包名称,显示用');
            $form->text('slug', __('Slug'))->required()->help('钱包代码,英文大写,不能有空格和特殊字符,例如 USDT');
            $form->number('decimal_places', __('Decimal places'))->default(5)->required()->help('小数点位数,虚拟币默认5位');
        }

        if ($form->isEditing()) {
            $form->display('name', __('Name'))->help('钱包名称,显示用');
            $form->display('slug', __('Slug'))->help('钱包代码,英文大写,不能有空格和特殊字符,例如 USDT');
            $form->display('decimal_places', __('Decimal places'))->default(5)->help('小数点位数,虚拟币默认5位');
        }

        $form->text('description', __('Description'))->help('钱包描述,可不填');
        $form->image('icon', __('Icon'))->move('wallet/icon')->uniqueName();
        //$form->number('is_enblened', __('Is enblened'));
        $states = [
            'on' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
        ];
        $form->switch('is_enblened', __('Is enblened'))->states($states);

        $form->hidden('sort', __('Sort'));

        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
                $pid = WalletType::orderBy('created_at', 'desc')->first();
                $form->sort = $pid->id + 1;
            });
        }

        $form->tools(function (Form\Tools $tools) {
//            $tools->disableList(); // 去掉`列表`按钮
            $tools->disableDelete(); // 去掉`删除`按钮
            $tools->disableView(); // 去掉`查看`按钮
        });
        $form->footer(function ($footer) {
//            $footer->disableReset();  // 去掉`重置`按钮
//            $footer->disableSubmit();   // 去掉`提交`按钮
            $footer->disableViewCheck(); // 去掉`查看`checkbox
            $footer->disableEditingCheck();  // 去掉`继续编辑`checkbox
            $footer->disableCreatingCheck();// 去掉`继续创建`checkbox
        });

        return $form;
    }
}
