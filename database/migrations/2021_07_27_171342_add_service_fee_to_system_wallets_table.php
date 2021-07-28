<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceFeeToSystemWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_wallets', function (Blueprint $table) {
            $table->decimal('service_fee', 32, 5)->default(0.00000)->comment('服务费')->after('commission_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_wallets', function (Blueprint $table) {
            //
        });
    }
}
