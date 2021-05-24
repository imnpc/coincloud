<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvatarToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('real_name')->nullable()->comment('真实姓名');
            $table->string('id_number')->nullable()->comment('身份证号');
            $table->string('id_front')->nullable()->comment('身份证正面');
            $table->string('id_back')->nullable()->comment('身份证反面');
            $table->tinyInteger('is_verify')->default(0)->comment('是否实名认证 0-未认证 1-已认证');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
