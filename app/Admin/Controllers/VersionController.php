<?php

namespace App\Admin\Controllers;

use App\Models\Version;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VersionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '客户端版本';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Version());

        $grid->column('platform', __('Platform'))->display(function ($value) {
            return Version::$platformMap[$value];
        });

        $grid->column('version', __('Version'));
        $grid->column('description', __('Description'));
        $grid->column('download_url', __('Url'));

        $states = [
            'on'  => ['value' => 0, 'text' => '不启用', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
        ];
        $grid->column('status')->switch($states);
        //$grid->column('status', __('Status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        //$grid->column('deleted_at', __('Deleted at'));

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
        $show = new Show(Version::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('platform', __('Platform'));
        $show->field('version', __('Version'));
        $show->field('description', __('Description'));
        $show->field('app', __('App'));
        $show->field('url', __('Url'));
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
        $form = new Form(new Version());

        $form->radioCard('platform', __('Platform'))->default(1)->options(['1' => 'Android', '2' => 'iOS'])->default('1')->required();
        //$form->radioCard('status', __('Status'))->options(['1' => 'Android', '2' => 'iOS'])->default('1')->required();
        $form->text('version', __('Version'))->required();
        $form->textarea('description', __('Description'))->required();
        $form->file('app', __('App包'))->move('download')->help('上传APP包和下载地址填写一个即可');
        $form->url('url', __('Url'));
        $states = [
            'on'  => ['value' => 0, 'text' => '不启用', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
        ];
        $form->switch('status', __('Status'))->states($states);

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
