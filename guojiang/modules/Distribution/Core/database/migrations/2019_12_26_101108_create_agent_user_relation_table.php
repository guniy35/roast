<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentUserRelationTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_user_relation')) {
			Schema::create($prefix . 'agent_user_relation', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('agent_id')->comment('分销商ID');
				$table->integer('user_id')->comment('用户ID');
				$table->tinyInteger('flag')->default(2)->comment('是否是新用户：1 是  2否');
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
		Schema::dropIfExists($prefix . 'agent_user_relation');
	}
}