<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceFeeToSystemWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_wallet_logs', function (Blueprint $table) {
            $table->decimal('old_service_fee', 16, 5)->default(0.00000)->comment('原有服务费')->after('old_commission_balance');
            $table->decimal('service_fee_add', 16, 5)->comment('服务费新增')->after('commission_balance_add');
            $table->decimal('service_fee', 16, 5)->comment('服务费')->after('commission_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_wallet_logs', function (Blueprint $table) {
            //
        });
    }
}
