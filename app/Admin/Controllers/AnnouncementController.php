<?php

namespace App\Admin\Controllers;

use App\Models\Announcement;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AnnouncementController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '公告';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Announcement());

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        //$grid->column('content', __('Content'));
        // 设置text、color、和存储值
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $grid->column('is_recommand', __('Is recommand'))->switch($states1);

        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => 0, 'text' => '不显示', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '显示', 'color' => 'primary'],
        ];
        $grid->column('status')->switch($states);
//        $grid->column('is_recommand', __('Is recommand'))->bool(['0' => false, '1' => true]);
//        $grid->column('status', __('是否显示'))->bool(['0' => false, '1' => true]);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        // $grid->column('deleted_at', __('Deleted at'));

        $grid->disableFilter();
        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器

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
        $show = new Show(Announcement::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('content', __('Content'));
        $show->field('is_recommand', __('Is recommand'));
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
        $form = new Form(new Announcement());

        $form->text('title', __('Title'))->required();
        $form->editor('content', __('Content'))->required();
        //$form->switch('is_recommand', __('Is recommand'));

        // 设置text、color、和存储值
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommand', __('Is recommand'))->states($states1);
        // 设置text、color、和存储值
        $states = [
            'on'  => ['value' => 0, 'text' => '不显示', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '显示', 'color' => 'primary'],
        ];
        $form->switch('status', __('Status'))->states($states);

//        $form->radioCard('is_recommand', __('Is recommand'))->options(['0' => '不推荐', '1' => '推荐'])->default('0')->required();
//        $form->radioCard('status', __('是否显示'))->options(['0' => '不显示', '1' => '显示'])->default('0')->required();
        //$form->switch('status', __('Status'));

        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();  // 去掉`列表`按钮
            $tools->disableDelete();  // 去掉`删除`按钮
            $tools->disableView();  // 去掉`查看`按钮
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
