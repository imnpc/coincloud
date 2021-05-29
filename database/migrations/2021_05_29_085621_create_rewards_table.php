<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('order_id')->comment('订单 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->decimal('power', 16, 5)->comment('购买算力');
            $table->string('reward_base')->comment('奖励基数');
            $table->decimal('coin_freed', 16, 5)->comment('奖励币总数量');
            $table->decimal('coin_freed_day', 16, 5)->comment('每日释放数量');
            $table->integer('days')->comment('所需天数');
            $table->integer('already_day')->comment('已释放天数');
            $table->decimal('already_coin', 16, 5)->comment('已释放数量');
            $table->decimal('wait_coin', 16, 5)->comment('等待释放数量');
            $table->tinyInteger('status')->default(0)->comment('0-释放中 1-释放完毕');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE rewards comment '奖励币'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rewards');
    }
}
