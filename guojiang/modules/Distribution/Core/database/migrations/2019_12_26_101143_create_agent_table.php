<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent')) {
			Schema::create($prefix . 'agent', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id')->comment('用户ID');
				$table->string('name')->comment('姓名');
				$table->string('mobile')->comment('手机号码');
				$table->string('note')->nullable()->comment('备注');
				$table->string('code')->nullable()->comment('编码');
				$table->tinyInteger('status')->default(0)->comment('状态 0：待审核  1：审核通过 2：审核不通过 3：清退');
				$table->integer('total_commission')->default(0)->comment('总佣金');
				$table->integer('balance')->default(0)->comment('佣金余额');
				$table->tinyInteger('type')->default(1)->comment('分销员类型：1 普通；2 机构；3 门店');
				$table->timestamps();
				$table->softDeletes();

				$table->unique('code', 'el_agent_code_unique');
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
		Schema::dropIfExists($prefix . 'agent');
	}
}