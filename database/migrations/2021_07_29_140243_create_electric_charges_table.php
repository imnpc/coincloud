<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectricChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electric_charges', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->string('year')->comment('年份');
            $table->string('month')->comment('月份');
            $table->decimal('electric_charge', 32, 12)->comment('电费单价');
            $table->integer('number')->default(0)->comment('数量');
            $table->decimal('total_fee', 32, 12)->default(0)->comment('本月累计电费');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE electric_charges comment '电费'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('electric_charges');
    }
}
