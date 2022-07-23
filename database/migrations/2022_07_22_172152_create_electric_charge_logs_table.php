<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectricChargeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electric_charge_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('electric_charge_id')->comment('所属电费 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->string('year')->comment('年份');
            $table->string('month')->comment('月份');
            $table->decimal('electric_charge', 32, 12)->comment('电费单价');
            $table->integer('number')->default(0)->comment('数量');
            $table->decimal('total_fee', 32, 12)->default(0)->comment('本月累计电费');
            $table->string('pay_image')->nullable()->comment('支付凭证图片');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->timestamp('confirm_time')->nullable()->comment('确认时间');
            $table->tinyInteger('pay_status')->default(1)->comment('支付状态 0-已完成 1-未提交 2-审核中');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('电费记录');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('electric_charge_logs');
    }
}
