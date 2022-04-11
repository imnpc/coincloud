<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeGasFeeToPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->decimal('pledge_fee', 16, 2)->default(0.00)->comment('每 T 所需质押币')->change();
            $table->decimal('pledge_coins', 16, 2)->default(0.00)->comment('质押币总数量')->change();
            $table->decimal('gas_fee', 16, 2)->default(0.00)->comment('每 T 所需 GAS 费')->change();
            $table->decimal('gas_coins', 16, 2)->default(0.00)->comment('GAS 费总数量')->change();
            $table->decimal('pledge_base', 16, 2)->default(0.00)->comment('基础质押金额')->change();
            $table->decimal('pledge_flow', 16, 2)->default(0.00)->comment('流量质押金额')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
        });
    }
}
