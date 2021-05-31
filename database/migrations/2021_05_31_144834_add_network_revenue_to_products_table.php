<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNetworkRevenueToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('network_revenue', 32, 5)->default(0.00000)->comment('全网24小时产出');
            $table->decimal('network_average_revenue', 32, 5)->default(0.00000)->comment('24小时平均挖矿收益');
            $table->decimal('network_valid_power', 32, 5)->default(0.00000)->comment('全网有效算力');
            $table->decimal('network_basic_rate', 32, 5)->default(0.00000)->comment('当前基础费率');
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
