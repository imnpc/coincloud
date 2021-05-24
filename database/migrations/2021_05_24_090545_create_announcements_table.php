<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->tinyInteger('is_recommand')->default(0)->comment('是否推荐 0-否 1-是');
            $table->tinyInteger('status')->default(0)->comment('是否显示 0-不显示 1-显示');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE announcements comment '公告'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}
