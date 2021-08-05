<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortToWalletTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet_types', function (Blueprint $table) {
            $table->integer('sort')->default(0)->comment('排序');
        });
        DB::update('update wallet_types set sort=id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_types', function (Blueprint $table) {
            //
        });
    }
}
