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
            $table->string('tag')->nullable()->comment('产品标签');
            $table->decimal('price', 16)->default(0)->comment('人民币价格');
            $table->decimal('price_usdt', 16)->default(0)->comment('USDT 价格');
            $table->decimal('price_coin', 16)->default(0)->comment('虚拟币价格');
            $table->string('coin_wallet_address')->nullable()->comment('虚拟币钱包地址');
            $table->string('coin_wallet_qrcode')->nullable()->comment('虚拟币钱包二维码');
            $table->integer('wallet_type_id')->comment('钱包类型 ID');
            $table->integer('wait_days')->default(0)->comment('等待天数');
            $table->integer('valid_days')->default(0)->comment('有效天数');
            $table->string('valid_days_text')->comment('有效天数文字');
            $table->string('choose_reason')->comment('必选理由');
            $table->string('choose_reason_text')->comment('必选理由文字');
            $table->decimal('service_rate', 5)->default(0)->comment('系统服务费比例');
            $table->decimal('pay_user_rate', 5)->default(0)->comment('客户每日收益比例');
            $table->decimal('now_rate', 5)->default(0)->comment('每日立即释放比例');
            $table->decimal('freed_rate', 5)->default(0)->comment('每日线性释放比例');
            $table->integer('freed_days')->default(0)->comment('线性释放天数');
            $table->decimal('parent1_rate', 5)->default(0)->comment('推荐分成1代');
            $table->decimal('parent2_rate', 5)->default(0)->comment('推荐分成2代');
            $table->decimal('invite_rate', 5)->default(0)->comment('邀请人奖励比例');
            $table->decimal('bonus_team_a', 5)->default(0)->comment('分红池 A 比例');
            $table->decimal('bonus_team_b', 5)->default(0)->comment('分红池 B 比例');
            $table->decimal('bonus_team_c', 5)->default(0)->comment('分红池 C 比例');
            $table->integer('upgrade_team_a')->default(0)->comment('分红池A所需推荐购买T数');
            $table->integer('upgrade_team_b')->default(0)->comment('分红池A所需推荐购买T数');
            $table->integer('upgrade_team_c')->default(0)->comment('分红池A所需推荐购买T数');
            $table->decimal('risk_rate', 5)->default(0)->comment('风控池比例');
            $table->decimal('gas_fee', 32)->default(0)->comment('每 T 所需 GAS 费');
            $table->decimal('pledge_fee', 32)->default(0)->comment('每 T 所需质押币');
            $table->integer('pledge_days')->default(1)->comment('质押币天数');
            $table->decimal('valid_rate', 5)->default(0)->comment('有效T数比例');
            $table->decimal('package_rate', 5)->default(0)->comment('封装比例');
            $table->string('thumb')->nullable()->comment('缩略图');
            $table->string('desc')->nullable()->comment('产品简介');
            $table->text('content')->nullable()->comment('产品详情');
            $table->string('total_revenue')->nullable()->default('0')->comment('矿池总产量');
            $table->string('yesterday_revenue')->nullable()->default('0')->comment('昨日产量');
            $table->string('yesterday_gas')->nullable()->default('0')->comment('昨日消耗GAS');
            $table->string('yesterday_efficiency')->nullable()->default('0')->comment('昨日挖矿效率');
            $table->string('total_revenue_text')->default('矿池总产量')->comment('矿池总产量_文字');
            $table->string('yesterday_revenue_text')->default('昨日产量')->comment('昨日产量_文字');
            $table->string('yesterday_gas_text')->default('昨日消耗GAS')->comment('昨日消耗GAS_文字');
            $table->string('yesterday_efficiency_text')->default('昨日挖矿效率')->comment('昨日挖矿效率_文字');
            $table->string('network_revenue')->nullable()->default('0')->comment('全网24小时产出');
            $table->string('network_average_revenue')->nullable()->default('0')->comment('24小时平均挖矿收益');
            $table->string('network_valid_power')->nullable()->default('0')->comment('全网有效算力');
            $table->string('network_basic_rate')->nullable()->default('0')->comment('当前基础费率');
            $table->string('unit')->default('T')->comment('单位');
            $table->tinyInteger('package_type')->default(0)->comment('状态 0-默认封装 1-客户封装');
            $table->tinyInteger('is_show_text')->default(1)->comment('显示算力文字提示 0-否 1-是');
            $table->integer('min_buy')->default(1)->comment('最低购买数量');
            $table->integer('stock')->default(0)->comment('库存');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('show_service_rate')->default(1)->comment('显示服务费 0-否 1-是');
            $table->integer('freed_wait_days')->default(0)->comment('释放等待天数');
            $table->tinyInteger('pledge_type')->default(0)->comment('质押模式 0-默认 1-混合');
            $table->decimal('pledge_base', 32)->default(0)->comment('基础质押金额');
            $table->decimal('pledge_flow', 32)->default(0)->comment('流量质押金额');
            $table->tinyInteger('is_sold_out')->default(0)->comment('是否已售罄 0-否 1-是');
            $table->tinyInteger('revenue_type')->default(0)->comment('收益类型 0-默认 1-pledge收满质押币');
            $table->tinyInteger('status')->default(1)->comment('状态 0-显示 1-隐藏');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('产品');
        });
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
