<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('article_category_id')->comment('文章分类 ID');
            $table->string('title')->comment('标题');
            $table->string('thumb')->nullable()->comment('缩略图');
            $table->string('desc')->nullable()->comment('内容简介');
            $table->text('content')->comment('内容');
            $table->tinyInteger('is_recommand')->default(0)->comment('是否推荐 0-否 1-是');
            $table->tinyInteger('status')->default(0)->comment('是否显示 0-不显示 1-显示');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('文章');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
