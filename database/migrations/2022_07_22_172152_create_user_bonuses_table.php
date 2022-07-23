<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bonuses', function (Blueprint $table) {
            $table->id();
            $table->date('day')->comment('日期');
            $table->integer('day_bonus_id')->comment('每日分红 ID');
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('product_id')->comment('所属产品 ID');
            $table->integer('order_id')->default(0)->comment('订单 ID');
            $table->decimal('bonus_coin_add', 16, 5)->comment('系统当日产币总数');
            $table->decimal('valid_power', 16, 5)->comment('有效T数');
            $table->decimal('each_add', 16, 5)->comment('当日每T产币量');
            $table->decimal('coins', 16, 5)->comment('当日产币量');
            $table->decimal('pay_user_rate', 5)->comment('用户收益比例');
            $table->decimal('coin_for_user', 16, 5)->comment('用户收益');
            $table->decimal('now_rate', 5)->comment('立即释放比例');
            $table->decimal('coin_now', 16, 5)->comment('立即释放数量');
            $table->decimal('freed_rate', 5)->comment('线性释放比例');
            $table->decimal('coin_freed', 16, 5)->comment('线性释放数量');
            $table->decimal('coin_freed_day', 16, 5)->comment('当日线性释放数量');
            $table->decimal('coin_freed_other', 16, 5)->comment('线性释放其他数量总计');
            $table->decimal('coin_day', 16, 5)->comment('当日可分配产币量');
            $table->decimal('balance', 16, 5)->comment('余额');
            $table->integer('parent1_uid')->comment('1代推荐人');
            $table->decimal('parent1_rate')->comment('1代推荐分成比例');
            $table->decimal('coin_parent1', 16, 5)->comment('1代推荐奖励');
            $table->integer('parent2_uid')->comment('2代推荐人');
            $table->decimal('parent2_rate')->comment('2代推荐分成比例');
            $table->decimal('coin_parent2', 16, 5)->comment('2代推荐奖励');
            $table->decimal('bonus_rate')->comment('分红池比例');
            $table->decimal('coin_bonus', 16, 5)->comment('分红池');
            $table->decimal('risk_rate')->comment('风控池比例');
            $table->decimal('coin_risk', 16, 5)->comment('风控池');
            $table->tinyInteger('status')->default(0)->comment('0-未执行 1-已执行');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户分成');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_bonuses');
    }
}
