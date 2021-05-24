<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('platform')->default(1)->comment('平台 1-Android 2-iOS');
            $table->string('version')->comment('版本号');
            $table->text('description')->comment('描述');
            $table->string('app')->nullable()->comment('APP压缩包');
            $table->string('url')->nullable()->comment('下载地址');
            $table->tinyInteger('status')->default(0)->comment('是否启用 0-否 1-是');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE versions comment '客户端版本'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('versions');
    }
}
