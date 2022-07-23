<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_wallets', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->comment('产品 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->decimal('team_a', 32, 5)->default(0)->comment('团队分红池A');
            $table->decimal('team_b', 32, 5)->default(0)->comment('团队分红池B');
            $table->decimal('team_c', 32, 5)->default(0)->comment('团队分红池C');
            $table->decimal('risk', 32, 5)->default(0)->comment('风控账户');
            $table->decimal('commission_balance', 32, 5)->default(0)->comment('推荐');
            $table->decimal('service_fee', 32, 5)->default(0)->comment('服务费');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('系统钱包');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_wallets');
    }
}
