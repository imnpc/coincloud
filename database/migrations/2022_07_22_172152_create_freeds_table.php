<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freeds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('user_bonus_id')->comment('所属用户分成 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->date('day')->comment('所属日期');
            $table->decimal('coins', 16, 5)->comment('当日总产币量');
            $table->integer('freed_rate')->comment('释放比例');
            $table->decimal('coin_freed', 16, 5)->comment('释放总数量');
            $table->decimal('coin_freed_day', 16, 5)->comment('每日释放数量');
            $table->decimal('other_fee', 16, 5)->comment('其他扣费');
            $table->integer('freed_wait_days')->default(0)->comment('释放等待天数');
            $table->integer('days')->comment('所需天数');
            $table->integer('already_day')->comment('已释放天数');
            $table->decimal('already_coin', 16, 5)->comment('已释放数量');
            $table->decimal('wait_coin', 16, 5)->comment('等待释放数量');
            $table->tinyInteger('status')->default(0)->comment('0-释放中 1-释放完毕');
            $table->index(['user_id', 'status', 'product_id']);
            $table->timestamps();
            $table->softDeletes();
            $table->comment('线性释放');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('freeds');
    }
}
