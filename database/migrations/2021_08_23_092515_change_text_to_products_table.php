<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTextToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('total_revenue')->nullable()->default(0)->comment('矿池总产量')->change();
            $table->string('yesterday_revenue')->nullable()->default(0)->comment('昨日产量')->change();
            $table->string('yesterday_gas')->nullable()->default(0)->comment('昨日消耗GAS')->change();
            $table->string('yesterday_efficiency')->nullable()->default(0)->comment('昨日挖矿效率')->change();
            $table->string('total_revenue_text' )->default('矿池总产量')->comment('矿池总产量_文字')->change();
            $table->string('yesterday_revenue_text')->default('昨日产量')->comment('昨日产量_文字')->change();
            $table->string('yesterday_gas_text')->default('昨日消耗GAS')->comment('昨日消耗GAS_文字')->change();
            $table->string('yesterday_efficiency_text')->default('昨日挖矿效率')->comment('昨日挖矿效率_文字')->change();
            $table->string('network_revenue')->nullable()->default(0)->comment('全网24小时产出')->change();
            $table->string('network_average_revenue')->nullable()->default(0)->comment('24小时平均挖矿收益')->change();
            $table->string('network_valid_power')->nullable()->default(0)->comment('全网有效算力')->change();
            $table->string('network_basic_rate')->nullable()->default(0)->comment('当前基础费率')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
