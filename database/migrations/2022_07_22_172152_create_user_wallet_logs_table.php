<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->integer('product_id')->default(0)->comment('产品 ID');
            $table->integer('order_id')->default(0)->comment('订单 ID');
            $table->integer('from_user_id')->nullable()->comment('来自用户 ID');
            $table->date('day')->nullable()->comment('所属日期');
            $table->decimal('old', 16, 5)->comment('原数值');
            $table->decimal('add', 16, 5)->comment('新增');
            $table->decimal('new', 16, 5)->comment('新数值');
            $table->tinyInteger('from')->default(0)->comment('来源');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户钱包日志');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_wallet_logs');
    }
}
