<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		/*售后主表*/
		if (!Schema::hasTable($prefix . 'refund')) {
			Schema::create($prefix . 'refund', function (Blueprint $table) {
				$table->increments('id');
				$table->string('refund_no')->comment('退款编号');
				$table->string('reason')->nullable()->comment('退款理由');
				$table->integer('amount')->nullable()->default(0)->comment('退款金额');
				$table->integer('user_id')->unsigned()->comment('申请人');
				$table->integer('admin_id')->unsigned()->nullable()->comment('处理人');
				$table->integer('order_id')->comment('订单ID');
				$table->integer('order_item_id')->comment('订单中具体的商品');
				$table->integer('type')->comment('1：退货， 2：换货，3：维修');
				$table->integer('quantity')->comment('数量');
				$table->text('content')->nullable()->comment('问题描述');
				$table->integer('status')->comment('1. 处理中  2.已完成  3.退款中 4上门取件 5 待您邮寄  6待您评价');//
				$table->text('images')->nullable();
				$table->timestamp('paid_time')->nullable()->comment('系统操作退款时间');
				$table->timestamps();
				$table->softDeletes();
			});
		}

		/*售后日志记录表*/
		if (!Schema::hasTable($prefix . 'refund_log')) {
			Schema::create($prefix . 'refund_log', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('refund_id')->comment('退款编号');
				$table->integer('user_id')->unsigned()->nullable()->comment('申请人');
				$table->integer('admin_id')->unsigned()->nullable()->comment('处理人');
				$table->string('action');
				$table->string('note')->nullable();
				$table->text('remark')->nullable();
				$table->timestamps();
				$table->softDeletes();
			});
		}

		/*售后退货物流记录表*/
		if (!Schema::hasTable($prefix . 'refund_shipping')) {
			Schema::create($prefix . 'refund_shipping', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('refund_id');
				$table->string('code')->nullable()->comment('快递公司编号');
				$table->string('shipping_name')->nullable()->comment('快递公司名称');
				$table->string('shipping_tracking')->nullable()->comment('运单编号');
				$table->timestamps();
			});
		}

		/*售后退货金额流向记录表*/
		if (!Schema::hasTable($prefix . 'refund_amount')) {
			Schema::create($prefix . 'refund_amount', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('amount')->default(0)->comment('金额,单位：分');
				$table->integer('refund_id')->comment('售后ID');
				$table->integer('order_id')->comment('订单ID');
				$table->string('type')->comment('类型：目前是 cash,balance');
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

		Schema::dropIfExists($prefix . 'refund');
		Schema::dropIfExists($prefix . 'refund_log');
		Schema::dropIfExists($prefix . 'refund_shipping');
		Schema::dropIfExists($prefix . 'refund_amount');
	}
}
