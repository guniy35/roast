<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if(!Schema::hasTable($prefix . 'payment')){

			Schema::create($prefix . 'payment', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('order_id')->unsigned(); //关联的订单号
				$table->string('order_type'); //关联的订单模型
				$table->string('channel');  //支付渠道
				$table->string('channel_no')->nullable(); //取单的支付单号
				$table->integer('amount');   //本次支付的金额
				$table->string('status');
				$table->text('details')->nullable();  //存储json meta 数据
				$table->timestamp('paid_at')->nullable(); //支付时间
				$table->timestamps();
				$table->softDeletes();
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

		Schema::drop($prefix . 'payment');
	}
}
