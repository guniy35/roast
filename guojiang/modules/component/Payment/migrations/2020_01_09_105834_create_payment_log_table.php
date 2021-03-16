<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentLogTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'payment_log')) {
			Schema::create($prefix . 'payment_log', function (Blueprint $table) {
				$table->increments('id');
				$table->string('action')->comment('create_charge 创建支付请求；result_pay 支付之后');
				$table->dateTime('operate_time')->comment('提交时间/支付时间');
				$table->string('order_no')->nullable()->comment('订单号');
				$table->string('transcation_order_no')->nullable()->comment('提交给微信的新的订单号');
				$table->string('transcation_no')->nullable()->comment('交易流水号');
				$table->integer('amount')->default(0)->comment('订单金额');
				$table->string('channel')->nullable()->comment('支付渠道 wx_pub_qr,wx_pub,wx_lite,alipay');
				$table->string('type')->nullable()->comment('订单类型：order，activity，recharge');
				$table->string('status')->nullable()->comment('状态：state，success，failed');
				$table->integer('user_id')->default(0)->comment('用户ID');
				$table->mediumText('meta')->nullable()->comment('记录微信、支付宝之后成功之后返回的所有数据');
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		Schema::dropIfExists($prefix . 'payment_log');
	}
}
