<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToUserWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_logs', function (Blueprint $table) {
            $table->tinyInteger('product_id')->default(0)->comment('产品 ID');
            $table->tinyInteger('order_id')->default(0)->comment('订单 ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_wallet_logs', function (Blueprint $table) {
            //
        });
    }
}
