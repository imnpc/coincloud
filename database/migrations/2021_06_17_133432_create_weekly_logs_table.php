<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('weekly_id')->comment('所属每周统计 ID');
            $table->string('year')->comment('年份');
            $table->string('week')->comment('周数');
            $table->string('begin')->comment('开始日期');
            $table->string('end')->comment('结束日期');
            $table->string('begin_time')->comment('开始时间');
            $table->string('end_time')->comment('结束时间');
            $table->decimal('freed', 16, 5)->comment('25%立即释放');
            $table->decimal('freed75', 16, 5)->comment('75%线性释放');
            $table->decimal('reward', 16, 5)->comment('奖励币');
            $table->decimal('total', 16, 5)->comment('总计');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE weekly_logs comment '每周数据详细列表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weekly_logs');
    }
}
