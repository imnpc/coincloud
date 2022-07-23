<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_rewards', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('reward_id')->comment('所属线性释放记录 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->date('day')->comment('所属日期');
            $table->decimal('coin', 16, 5)->comment('释放数量');
            $table->integer('today')->comment('第几天');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('奖励币释放记录');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_rewards');
    }
}
