<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_bonuses', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->comment('产品 ID');
            $table->date('day')->comment('日期');
            $table->decimal('total_power', 16, 5)->default(0.00000)->comment('有效算力总数');
            $table->decimal('power_add', 16, 5)->default(0.00000)->comment('新增算力');
            $table->decimal('coin_add', 16, 5)->default(0.00000)->comment('产币数量');
            $table->decimal('efficiency', 16, 5)->default(0.00000)->comment('挖矿效率');
            $table->decimal('cost', 16, 5)->default(0.00000)->comment('挖矿成本');
            $table->decimal('fee', 16, 5)->default(0.00000)->comment('额外扣除');
            $table->decimal('day_price', 16, 5)->default(0.00000)->comment('当天币价');
            $table->decimal('day_pledge', 16, 5)->default(0.00000)->comment('当天质押币系数');
            $table->decimal('day_cost', 16, 5)->default(0.00000)->comment('当天单T封装成本');
            $table->string('remark')->nullable()->comment('备注');
            $table->tinyInteger('status')->default(0)->comment('0-未执行 1-已执行');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE day_bonuses comment '每日分成'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('day_bonuses');
    }
}
