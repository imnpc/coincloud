<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigninLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signin_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment("用户 ID");
            $table->ipAddress('ip')->default('')->comment("IP 地址");
            $table->string('ip_area')->comment("IP 地区");
            $table->timestamp('login_at')->nullable()->comment("登录时间");
            $table->integer('client_id')->comment('登录客户端');
        });
        DB::statement("ALTER TABLE signin_logs comment '用户登录日志'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signin_logs');
    }
}
