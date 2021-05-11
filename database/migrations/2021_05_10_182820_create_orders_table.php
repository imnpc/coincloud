<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_sn')->comment('订单编号');
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->integer('number')->comment('购买数量');
            $table->decimal('pay_money', 8, 2)->default(0.00)->comment('支付金额');
            $table->integer('wait_days')->default(0)->comment('等待天数');
            $table->tinyInteger('wait_status')->default(0)->comment('等待状态0-已生效 1-等待中');
            $table->integer('valid_days')->default(0)->comment('有效天数');
            $table->decimal('valid_rate', 8, 2)->default(0.00)->comment('有效T数比例');
            $table->decimal('valid_power', 16, 2)->comment('当前有效T数');
            $table->decimal('max_valid_power', 16, 2)->comment('最大有效T数');
            $table->decimal('package_rate', 8, 2)->default(0.00)->comment('封装比例');
            $table->decimal('package_already', 32, 5)->comment('已封装数量');
            $table->decimal('package_wait', 32, 5)->comment('等待封装数量');
            $table->tinyInteger('package_status')->default(0)->comment('封装状态 0-封装完成 1-等待封装 2-封装中');
            $table->tinyInteger('pay_status')->default('0')->comment('支付状态 0-已完成 1-未提交 2-审核中');
            $table->string('pay_image')->nullable()->comment('支付凭证图片');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->timestamp('confirm_time')->nullable()->comment('确认时间');
            $table->tinyInteger('is_output_coin')->default(0)->comment('是否产币 0-是 1-否');
            $table->string('remark')->nullable()->comment('备注');
            $table->tinyInteger('status')->default(0)->comment('订单状态 0-有效 1-无效');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE orders comment '订单'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
