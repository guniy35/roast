<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceTables extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'balance')) {
			Schema::create($prefix . 'balance', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('user_id')->comment('用户id，外键关联 user 表 id 字段');
				$table->string('type')->default('expend')->comment('余额变动的类型：recharge(充值), expend(消费)');
				$table->string('note')->nullable()->comment('文字描述，展示给用户');
				$table->integer('value')->default(0)->comment('单位：分');
				$table->unsignedInteger('current_balance')->default(0)->comment('当前余额');
				$table->unsignedInteger('origin_id')->default(0)->comment('引起变动的数据来源的id');
				$table->string('origin_type')->nullable()->comment('引起变动的数据来源类型，值通常为 class 全称');
				$table->string('channel')->nullable()->comment('渠道：可能的值 online (线上) offline (线下)，');
				$table->timestamps();
				$table->softDeletes();
			});
		}

		if (!Schema::hasTable($prefix . 'balance_cash')) {
			Schema::create($prefix . 'balance_cash', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('user_id');
				$table->string('cash_no')->nullable();
				$table->string('cash_type')->default('customer_account'); //提现到账类型：自定义账户；微信钱包 customer_wechat
				$table->unsignedInteger('agent_id')->default(0);
				$table->integer('amount')->default(0)->comment('金额');
				$table->integer('balance')->default(0)->comment('余额');
				$table->tinyInteger('status')->default(0)->comment('状态 0：待审核  1:待打款提现 2：已打款提现  3:审核不通过');
				$table->timestamp('settle_time')->nullable()->comment('打款时间');
				$table->string('cert')->nullable()->comment('打款凭证');
				$table->integer('bank_account_id')->default(0)->comment('账号ID');
				$table->string('bank_name')->nullable()->comment('银行名称');
				$table->string('bank_number')->nullable()->comment('账号');
				$table->string('owner_name')->nullable()->comment('收款姓名');
				$table->timestamps();
				$table->softDeletes();
			});
		}

		if (!Schema::hasTable($prefix . 'balance_order')) {
			Schema::create($prefix . 'balance_order', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('user_id')->comment('用户id，外键关联 user 表 id 字段');
				$table->string('order_no')->comment('订单编号');
				$table->string('pay_type')->nullable()->comment('支付方式 包含支付宝，微信，余额支付');
				$table->unsignedTinyInteger('pay_status')->default(0)->comment('支付状态，0 待支付，1 已支付 2 已退款');
				$table->timestamp('pay_time')->nullable()->comment('完成支付时间');
				$table->unsignedInteger('amount')->comment('实际到账金额，单位:分');
				$table->unsignedInteger('pay_amount')->comment('实际支付金额，单位:分');
				$table->unsignedInteger('origin_id')->nullable()->comment('支付优惠活动的id');
				$table->string('origin_type')->nullable()->comment('支付优惠活动类型的class name');
				$table->string('note')->nullable()->comment('备注信息');
				$table->timestamps();
				$table->softDeletes();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		Schema::dropIfExists($prefix . 'balance');
		Schema::dropIfExists($prefix . 'balance_cash');
		Schema::dropIfExists($prefix . 'balance_order');
	}
}
