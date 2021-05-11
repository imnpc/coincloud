<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('产品名称');
            $table->string('tag')->comment('产品标签');
            $table->decimal('price', 8, 2)->default(0.00)->comment('人民币价格');
            $table->decimal('price_usdt', 8, 2)->default(0.00)->comment('USDT 价格');
            $table->decimal('price_coin', 8, 2)->default(0.00)->comment('虚拟币价格');
            $table->string('coin_wallet_address')->nullable()->comment('虚拟币钱包地址');
            $table->string('coin_wallet_qrcode')->nullable()->comment('虚拟币钱包二维码');
            $table->integer('wallet_type_id')->comment('钱包类型 ID');
            $table->integer('wait_days')->default(0)->comment('等待天数');
            $table->integer('valid_days')->default(0)->comment('有效天数');
            $table->string('valid_days_text')->comment('有效天数文字');
            $table->string('choose_reason')->comment('必选理由');
            $table->string('choose_reason_text')->comment('必选理由文字');
            $table->decimal('service_rate', 8, 2)->default(0.00)->comment('系统服务费比例');
            $table->decimal('day_customer_rate', 8, 2)->default(0.00)->comment('客户每日收益比例');
            $table->decimal('day_rate', 8, 2)->default(0.00)->comment('每日立即释放比例');
            $table->decimal('freed_rate', 8, 2)->default(0.00)->comment('每日线性释放比例');
            $table->decimal('parent1', 8, 2)->default(0.00)->comment('推荐分成1代');
            $table->decimal('parent2', 8, 2)->default(0.00)->comment('推荐分成2代');
            $table->decimal('invite_rate', 8, 2)->default(0.00)->comment('邀请人奖励比例');
            $table->decimal('bonus_team_a', 8, 2)->default(0.00)->comment('分红池 A 比例');
            $table->decimal('bonus_team_b', 8, 2)->default(0.00)->comment('分红池 B 比例');
            $table->decimal('bonus_team_c', 8, 2)->default(0.00)->comment('分红池 C 比例');
            $table->integer('upgrade_team_a')->default(0)->comment('分红池A所需推荐购买T数');
            $table->integer('upgrade_team_b')->default(0)->comment('分红池A所需推荐购买T数');
            $table->integer('upgrade_team_c')->default(0)->comment('分红池A所需推荐购买T数');
            $table->decimal('risk_rate', 8, 2)->default(0.00)->comment('风控池比例');
            $table->decimal('gas_fee', 32, 5)->default(0.00000)->comment('每 T 所需 GAS 费');
            $table->decimal('pledge_fee', 32, 5)->default(0.00000)->comment('每 T 所需质押币');
            $table->integer('pledge_days')->default('1')->comment('质押币天数');
            $table->decimal('valid_rate', 8, 2)->default(0.00)->comment('有效T数比例');
            $table->decimal('package_rate', 8, 2)->default(0.00)->comment('封装比例');
            $table->string('thumb')->nullable()->comment('缩略图');
            $table->string('desc')->nullable()->comment('产品简介');
            $table->text('content')->nullable()->comment('产品详情');
            $table->tinyInteger('status')->default(1)->comment('状态 0-显示 1-隐藏');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE products comment '产品'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
