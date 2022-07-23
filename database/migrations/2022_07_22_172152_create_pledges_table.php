<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('order_id')->comment('订单 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->decimal('power', 16, 0)->comment('购买算力');
            $table->decimal('pledge_fee', 16)->default(0)->comment('每 T 所需质押币');
            $table->decimal('pledge_coins', 16)->default(0)->comment('质押币总数量');
            $table->integer('pledge_days')->comment('所需天数');
            $table->decimal('gas_fee', 16)->default(0)->comment('每 T 所需 GAS 费');
            $table->decimal('gas_coins', 16)->default(0)->comment('GAS 费总数量');
            $table->tinyInteger('pledge_type')->default(0)->comment('质押模式 0-默认 1-混合');
            $table->decimal('pledge_base', 16)->default(0)->comment('基础质押金额');
            $table->decimal('pledge_flow', 16)->default(0)->comment('流量质押金额');
            $table->integer('wait_days')->comment('剩余天数');
            $table->tinyInteger('status')->default(0)->comment('0-质押中 1-已完成退回');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('质押币');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledges');
    }
}
