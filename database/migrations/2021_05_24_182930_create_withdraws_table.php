<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('所属用户 ID');
            $table->integer('wallet_type_id')->comment('支付方式钱包类型 ID');
            $table->string('image')->comment('提币二维码图片');
            $table->string('wallet_address')->comment('提币钱包地址');
            $table->decimal('coin', 32, 5)->comment('提币金额');
            $table->decimal('fee', 32, 5)->comment('手续费');
            $table->decimal('real_coin', 32, 5)->comment('到账金额');
            $table->string('reason')->nullable()->comment('取消原因');
            $table->timestamp('canceled_time')->nullable()->comment('取消时间');
            $table->tinyInteger('status')->default(0)->comment('0-待审核 1-已转账 2-驳回申请');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE withdraws comment '提币记录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
    }
}
