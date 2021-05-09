<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('钱包名称');
            $table->string('slug')->unique()->comment('钱包代码,唯一');
            $table->string('description')->nullable()->comment('钱包说明');
            $table->integer('decimal_places')->default(5)->comment('钱包小数点');
            $table->integer('is_enblened')->default(0)->comment('是否启用 0-禁用 1-启用');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE wallet_types comment '钱包类型'");
        DB::insert("insert into wallet_types(id,name,slug,description,decimal_places,is_enblened)
			values(?,?,?,?,?,?)", [1, '金钱', 'MONEY', '金钱', 2, 1]);
        DB::insert("insert into wallet_types(id,name,slug,description,decimal_places,is_enblened)
			values(?,?,?,?,?,?)", [2, '积分', 'CREDIT', '积分', 2, 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_types');
    }
}
