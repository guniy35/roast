<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentCashTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_cash')) {
			Schema::create($prefix . 'agent_cash', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('agent_id')->comment('分销商ID');
				$table->integer('amount')->default(0)->comment('金额');
				$table->integer('balance')->default(0)->comment('余额');
				$table->tinyInteger('status')->default(0)->comment('状态 0：待审核  1:待打款提现 2：已打款提现  3:审核不通过');
				$table->dateTime('settle_time')->nullable()->comment('打款时间');
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
		Schema::dropIfExists($prefix . 'agent_cash');
	}
}