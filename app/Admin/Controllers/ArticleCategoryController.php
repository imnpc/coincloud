<?php

namespace App\Admin\Controllers;

use App\Models\ArticleCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Layout\Content;

class ArticleCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '文章分类';

    public function index(Content $content)
    {
        $tree = new Tree(new ArticleCategory);

        return $content
            ->header('文章分类')
            ->body($this->tree());
    }

    /**
     * Make a grid builder.
     *
     * @return Tree
     */
    protected function tree()
    {
        return ArticleCategory::tree(function (Tree $tree) {

            $tree->branch(function ($branch) {

                $icon = "";
                if ($branch['icon_url']) {
                    $src = $branch['icon_url'];
                    $icon = "<img src='$src' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
                }
                $status = "";
                if ($branch['status']) {
                    $status = '<i class="fa fa-check text-green"></i>';
                } else {
                    $status = '<i class="fa fa-close text-red"></i>';
                }
                return "{$branch['id']}  &nbsp;&nbsp;<strong>{$branch['title']}</strong>&nbsp;&nbsp;  $icon &nbsp;&nbsp; $status"; // 标题添加strong标签
            });

        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ArticleCategory());

        $grid->column('id', __('Id'));
//        $grid->column('parent_id', __('Parent id'));
        $grid->column('order', __('Order'));
        $grid->column('title', __('Title'));
        $grid->column('status', __('是否显示'))->bool(['0' => false, '1' => true]);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

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
        $show = new Show(ArticleCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('parent_id', __('Parent id'));
        $show->field('order', __('Order'));
        $show->field('title', __('Title'));
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
        $form = new Form(new ArticleCategory());

//        $form->switch('parent_id', __('Parent id'));
//        $form->switch('order', __('Order'));
        $form->text('title', __('Title'));
        $form->image('icon', __('Icon'))->move('article/icon')->uniqueName();
        $form->radioCard('status', __('是否显示'))->options(['0' => '不显示', '1' => '显示'])->default('0')->required();
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
