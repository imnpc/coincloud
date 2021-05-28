<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalRevenueToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('total_revenue', 32, 5)->default(0.00000)->comment('矿池总产量');
            $table->decimal('yesterday_revenue', 32, 5)->default(0.00000)->comment('昨日产量');
            $table->decimal('yesterday_gas', 32, 5)->default(0.00000)->comment('昨日消耗GAS');
            $table->decimal('yesterday_efficiency', 32, 5)->default(0.00000)->comment('昨日挖矿效率');
            $table->string('total_revenue_text', 32, 5)->default('矿池总产量')->comment('矿池总产量_文字');
            $table->string('yesterday_revenue_text', 32, 5)->default('昨日产量')->comment('昨日产量_文字');
            $table->string('yesterday_gas_text', 32, 5)->default('昨日消耗GAS')->comment('昨日消耗GAS_文字');
            $table->string('yesterday_efficiency_text', 32, 5)->default('昨日挖矿效率')->comment('昨日挖矿效率_文字');
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
