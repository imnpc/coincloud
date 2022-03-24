<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use solutionforest\LaravelAdmin\Translatable\Extensions\Form\TForm;
use solutionforest\LaravelAdmin\Translatable\Extensions\FormLangSwitcher;
use solutionforest\LaravelAdmin\Translatable\Extensions\TranslatableForm;

class ArticleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '文章';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article());

        $grid->column('id', __('Id'));
        $grid->column('thumb', __('Thumb'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $src = $this->thumb_url;
                $icon = "<img src='$src' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('article_category_id', __('Article category id'))->display(function ($category_id) {
            $category = ArticleCategory::find($category_id);
            if ($category) {
                return ArticleCategory::find($category_id)->title;
            } else {
                return "";
            }
        });
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

        //$grid->column('status', __('Status'))->bool(['0' => false, '1' => true]);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        //$grid->column('deleted_at', __('Deleted at'));

        //        $grid->disableFilter(); // 禁用查询过滤器
        $grid->disableRowSelector(); // 禁用行选择checkbox
//        $grid->disableCreateButton(); // 禁用创建按钮
//        $grid->disableActions(); // 禁用行操作列
        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->actions(function ($actions) {
//            $actions->disableDelete();// 去掉删除
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
        $show = new Show(Article::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('article_category_id', __('Article category id'));
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
//        $form = new Form(new Article());
        $form = new TForm(new Article());
        // 加入 轉換語言
        $form->header(function (Form\Tools $tools) {
            $tools->append((new FormLangSwitcher())->render());
        });

        $form->select('article_category_id', __('Article category id'))->options(ArticleCategory::selectOptions())->required();
//        $form->text('title', __('Title'))->required();
        $form->translatable(function (TranslatableForm $form) {
            $form->text('title', __('Title'))->required();
        });
        $form->image('thumb', __('Thumb'))->move('article/thumb')->uniqueName();

//        $form->textarea('desc', __('Desc'));
//        $form->editor('content', __('Content'))->required();
        $form->translatable(function (TranslatableForm $form) {
            $form->textarea('desc', __('Desc'));
            $form->editor('content', __('Content'));
        });
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
