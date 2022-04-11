<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductIdToUserWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_logs', function (Blueprint $table) {
            $table->integer('product_id')->default(0)->comment('产品 ID')->change();
            $table->integer('order_id')->default(0)->comment('订单 ID')->change();
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
