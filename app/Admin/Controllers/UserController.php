<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Destroy;
use App\Admin\Actions\Restore;
use App\Admin\Actions\User\Money;
use App\Admin\Actions\User\WalletLog;
use App\Admin\Extensions\ShowOrder;
use App\Admin\Extensions\ShowTeam;
use App\Models\Level;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->filter(function ($filter) {
            $filter->scope('trashed', '回收站')->onlyTrashed();
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('id', __('Id'));
            $filter->equal('parent_id', __('Parent id'));
            $filter->like('real_name', __('Real name'));
            $filter->like('mobile', __('Mobile'));
            $filter->equal('is_verify', __('是否实名'))->radio([
                '' => '所有',
                0 => '未实名',
                1 => '已实名',
            ]);
        });

        $grid->column('id', __('Id'));
        $grid->column('avatar_url', __('Avatar'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('name', __('Name'))->modal('用户的团队列表', ShowTeam::class);
//        $grid->column('email', __('Email'));
//        $grid->column('email_verified_at', __('Email verified at'));
//        $grid->column('password', __('Password'));
//        $grid->column('remember_token', __('Remember token'));
        $grid->column('mobile', __('Mobile'))->modal('用户订单', ShowOrder::class);
//        $grid->column('nickname', __('Nickname'));
        $grid->column('parent_id', __('Parent id'));
        $grid->column('level.name', __('Level id'));
        $grid->column('is_verify', __('是否实名认证'))->bool(['0' => false, '1' => true]);
        $grid->column('show_pledge', __('Show pledge'))->bool(['0' => false, '1' => true]);
        $grid->column('is_banned', __('Is banned'))->bool(['0' => false, '1' => true]);
        $grid->column('last_login_at', __('Last login at'));
        $grid->column('last_login_ip', __('Last login ip'));
        $grid->column('status', __('Status'))->using([
            0 => '启用',
            1 => '禁用',
        ], '未知')->label([
            0 => 'success',
            1 => 'danger',
        ], 'warning');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
//        $grid->column('deleted_at', __('Deleted at'));

//        $grid->disableFilter(); // 禁用查询过滤器
//        $grid->disableRowSelector(); // 禁用行选择checkbox
//        $grid->disableCreateButton(); // 禁用创建按钮
//        $grid->disableActions(); // 禁用行操作列
//        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器

        $grid->actions(function ($actions) {
            if ($actions->row['is_verify'] == 1) {
                $actions->disableDelete();// 去掉删除
            }
            $actions->disableView();// 去掉查看
//            $actions->disableEdit();// 去掉编辑
            if (\request('_scope_') == 'trashed') {
                $actions->disableDelete();// 去掉删除
                $actions->disableEdit();// 去掉编辑
                $actions->add(new Restore());
                $actions->add(new Destroy());
            }
            $actions->add(new WalletLog);
            $actions->add(new Money);
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('mobile', __('Mobile'));
        $show->field('nickname', __('Nickname'));
        $show->field('parent_id', __('Parent id'));
        $show->field('status', __('Status'));
        $show->field('last_login_at', __('Last login at'));
        $show->field('last_login_ip', __('Last login ip'));
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
        $form = new Form(new User());

        $form->display('avatar_url', __('头像'))->with(function ($value) {
            return "<img src='$value' style='max-width:120px;max-height:120px;text-align: left' class='img'/>";
        });
        $form->text('name', __('Name'));
//        $form->email('email', __('Email'));
//        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
//        $form->password('password', __('Password'));
//        $form->text('remember_token', __('Remember token'));
//        $form->mobile('mobile', __('Mobile'));
        $form->text('nickname', __('Nickname'));
        if ($form->isEditing()) {
            $form->display('mobile', __('Mobile'));
            $form->password('password', __('Password'))->help('不修改密码无需填写 默认密码 123456789');
        }
        if ($form->isCreating()) {
            $form->mobile('mobile', __('Mobile'))->required();
            $form->password('password', __('Password'))->default('123456')->required()->help('默认密码 123456');
        }

        $form->number('parent_id', __('Parent id'))->default(0);
//        $form->select('parent_id', __('Parent id'))->options(function ($id) {
//            $user = User::find($id);
//            if ($user) {
//                return [$user->id => $user->mobile];
//            }
//        })->ajax('/admin/api/users');
        $form->select('level_id', __('Level id'))->options(Level::all()->pluck('name', 'id'))->required();
        $form->text('real_name', __('Real name'));
        $form->text('id_number', __('Id number'));
        $form->display('id_front_url', __('身份证正面'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->display('id_back_url', __('身份证反面'))->with(function ($value) {
            return "<img src='$value' width='100%'/>";
        });
        $form->radioCard('is_verify', __('Is verify'))->options(['0' => '未认证', '1' => '已认证'])->default('0');
        $form->radioCard('show_pledge', __('Show pledge'))->options(['0' => '不显示', '1' => '显示'])->default('1');
        $form->radioCard('is_banned', __('Is banned'))->options(['0' => '否', '1' => '是'])->default('0');

        $states = [
            'on' => ['value' => 0, 'text' => '启用', 'color' => 'primary'],
            'off' => ['value' => 1, 'text' => '禁用', 'color' => 'danger'],
        ];

        $form->switch('status', __('Status'))->states($states);
        $form->display('last_login_at', __('Last login at'));
        $form->display('last_login_ip', __('Last login ip'));

        // 保存前回调 设置未填写参数的默认值
        $form->saving(function (Form $form) {
//            if (empty($form->nickname)) {
//                $form->nickname = $form->mobile;
//            }
//            if (empty($form->name)) {
//                $form->name = $form->mobile;
//            }
            // 密码修改
            if (empty($form->input('password'))) {
                $form->input('password', $form->model()->password);
            } elseif ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

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

    public function users(Request $request)
    {
        $q = $request->get('q');
        return User::where('mobile', 'like', "%$q%")->paginate(null, ['id', 'mobile as text']);
    }
}
