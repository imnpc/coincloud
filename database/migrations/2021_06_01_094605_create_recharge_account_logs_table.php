<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRechargeAccountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_account_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('recharge_id')->comment('所属充值 ID');
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->date('day')->nullable()->comment('所属日期');
            $table->decimal('power', 32, 5)->comment('封装T数');
            $table->decimal('day_pledge', 32, 5)->comment('当天质押币系数');
            $table->decimal('day_gas', 32, 5)->comment('当天单T有效算力封装成本');
            $table->decimal('pledge', 32, 5)->comment('质押币');
            $table->decimal('gas', 32, 5)->comment('GAS 费');
            $table->decimal('total', 32, 5)->comment('总计');
            $table->decimal('used', 32, 5)->comment('当日总封装T数');
            $table->decimal('day_limit', 32, 5)->comment('当日最大封装T数');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE recharge_account_logs comment '充值封装日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharge_account_logs');
    }
}
