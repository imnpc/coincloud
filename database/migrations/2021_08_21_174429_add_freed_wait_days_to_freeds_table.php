<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreedWaitDaysToFreedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freeds', function (Blueprint $table) {
            $table->integer('freed_wait_days')->default(0)->comment('释放等待天数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freeds', function (Blueprint $table) {
            //
        });
    }
}
