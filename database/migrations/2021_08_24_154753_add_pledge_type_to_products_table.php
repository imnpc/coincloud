<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPledgeTypeToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('pledge_type')->default(0)->comment('质押模式 0-默认 1-混合');
            $table->decimal('pledge_base', 32, 0)->default(0)->comment('基础质押金额');
            $table->decimal('pledge_flow', 32, 0)->default(0)->comment('流量质押金额');
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
