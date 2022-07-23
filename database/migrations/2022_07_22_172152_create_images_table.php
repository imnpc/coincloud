<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->comment('用户 ID');
            $table->string('type')->index()->comment('类型');
            $table->string('path')->comment('路径');
            $table->string('disk')->comment('磁盘名');
            $table->string('size')->comment('大小');
            $table->double('size_kb', 8, 2)->default(0)->comment('大小KB');
            $table->timestamps();
            $table->comment('图片附件表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
