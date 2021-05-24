<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->text('content')->comment('反馈内容');
            $table->text('reply')->nullable()->comment('网站回复');
            $table->tinyInteger('is_show')->default(0)->comment('是否显示 0-等待回复 1-已回复');
            $table->tinyInteger('status')->default(0)->comment('是否显示 0-不显示 1-显示');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE feedback comment '问题反馈'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
