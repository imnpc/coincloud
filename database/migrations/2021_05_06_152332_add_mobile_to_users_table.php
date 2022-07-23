<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->unique()->comment('手机号码');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->integer('parent_id')->nullable()->default(0)->comment('上级 ID');
            $table->integer('status')->default(0)->comment('状态 0-启用 1-禁用');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->string('last_login_ip', 45)->nullable()->comment('最后登录IP');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('real_name')->nullable()->comment('真实姓名');
            $table->string('id_number')->nullable()->comment('身份证号');
            $table->string('id_front')->nullable()->comment('身份证正面');
            $table->string('id_back')->nullable()->comment('身份证反面');
            $table->tinyInteger('is_verify')->default(0)->comment('是否实名认证 0-未认证 1-已认证');
            $table->string('money_password')->nullable()->comment('资金密码');
            $table->string('keyword')->nullable()->comment('关键字');
            $table->tinyInteger('show_pledge')->default(1)->comment('显示质押币 0-否 1-是');
            $table->integer('level_id')->default(1)->comment('用户等级 级差用');
            $table->integer('team_id')->default(0)->comment('用户团队 级差用');
            $table->integer('is_banned')->default(0)->comment('是否封禁用户 0-否 1-是');
            $table->softDeletes();
            $table->comment('用户');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
