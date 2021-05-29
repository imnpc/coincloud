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
            $table->decimal('power', 16, 5)->comment('购买算力');
            $table->string('pledge_fee')->comment('每 T 所需质押币');
            $table->decimal('coins', 16, 5)->comment('质押币总数量');
            $table->integer('pledge_days')->comment('所需天数');
            $table->tinyInteger('status')->default(0)->comment('0-质押中 1-已完成退回');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE pledges comment '质押币'");
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
