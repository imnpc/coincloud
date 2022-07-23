<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('parent_id')->default(0)->comment('上级 ID');
            $table->tinyInteger('order')->default(0)->comment('排序');
            $table->string('title')->comment('分类名称');
            $table->string('icon')->nullable()->comment('分类图标');
            $table->tinyInteger('status')->default(0)->comment('是否显示 0-不显示 1-显示');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('文章分类');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_categories');
    }
}
