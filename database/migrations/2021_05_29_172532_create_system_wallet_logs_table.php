<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('system_wallet_id')->comment('系统钱包 ID');
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->date('day')->nullable()->comment('所属日期');
            $table->decimal('old_team_a', 16, 5)->default(0.00000)->comment('原有分红池A');
            $table->decimal('old_team_b', 16, 5)->default(0.00000)->comment('原有分红池B');
            $table->decimal('old_team_c', 16, 5)->default(0.00000)->comment('原有分红池C');
            $table->decimal('old_risk', 16, 5)->default(0.00000)->comment('原有风控账户');
            $table->decimal('old_commission_balance', 16, 5)->default(0.00000)->comment('原有推荐');
            $table->decimal('team_a_add', 16, 5)->comment('分红池A新增');
            $table->decimal('team_b_add', 16, 5)->comment('分红池B新增');
            $table->decimal('team_c_add', 16, 5)->comment('分红池C新增');
            $table->decimal('risk_add', 16, 5)->comment('风控账户新增');
            $table->decimal('commission_balance_add', 16, 5)->comment('推荐新增');
            $table->decimal('team_a', 16, 5)->comment('分红池A');
            $table->decimal('team_b', 16, 5)->comment('分红池B');
            $table->decimal('team_c', 16, 5)->comment('分红池C');
            $table->decimal('risk', 16, 5)->comment('风控账户');
            $table->decimal('commission_balance', 16, 5)->comment('推荐');
            $table->integer('from_user_id')->nullable()->comment('来自用户 ID');
            $table->integer('order_id')->nullable()->comment('来自订单 ID');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE system_wallet_logs comment '系统钱包日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_wallet_logs');
    }
}
