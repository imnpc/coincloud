<?php

namespace App\Admin\Controllers;

use App\Models\Feedback;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FeedbackController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '问题反馈';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Feedback());


        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('user_id', __('User id'));
        });


        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('user.mobile', __('用户'));
        $grid->column('content', __('Content'));
        $grid->column('reply', __('Reply'));

        // 设置text、color、和存储值 是否显示 0-等待回复 1-已回复
        $states1 = [
            'on' => ['value' => 0, 'text' => '等待回复', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已回复', 'color' => 'primary'],
        ];
        $grid->column('is_show')->switch($states1);
        //$grid->column('is_show', __('Is show'));
        // 设置text、color、和存储值
        $states = [
            'on' => ['value' => 0, 'text' => '不显示', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '显示', 'color' => 'primary'],
        ];
        $grid->column('status')->switch($states);
        //$grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        //$grid->column('deleted_at', __('Deleted at'));

//        $grid->disableFilter(); // 禁用查询过滤器
        $grid->disableRowSelector(); // 禁用行选择checkbox
        $grid->disableCreateButton(); // 禁用创建按钮
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
        $show = new Show(Feedback::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('content', __('Content'));
        $show->field('reply', __('Reply'));
        $show->field('is_show', __('Is show'));
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
        $form = new Form(new Feedback());

        //$form->number('user_id', __('User id'));
        $form->display('user.mobile', __('User id'));
        $form->display('content', __('Content'));
        $form->textarea('reply', __('Reply'));
        // 设置text、color、和存储值 是否显示 0-等待回复 1-已回复
        $states1 = [
            'on' => ['value' => 0, 'text' => '等待回复', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已回复', 'color' => 'primary'],
        ];
        $form->switch('is_show', __('Is show'))->states($states1);
        //$form->switch('is_show', __('Is show'));
        $states = [
            'on' => ['value' => 0, 'text' => '不显示', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '显示', 'color' => 'primary'],
        ];
        $form->switch('status', __('Status'))->states($states);
        //$form->switch('status', __('Status'));

        $form->tools(function (Form\Tools $tools) {
//            $tools->disableList();  // 去掉`列表`按钮
//            $tools->disableDelete();  // 去掉`删除`按钮
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
