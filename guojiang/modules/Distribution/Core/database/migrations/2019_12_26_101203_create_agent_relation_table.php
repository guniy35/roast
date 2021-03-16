<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentRelationTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_relation')) {
			Schema::create($prefix . 'agent_relation', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('level')->comment('相对等级');
				$table->integer('parent_agent_id')->comment('agent_id的父ID');
				$table->integer('agent_id')->comment('分销商ID');
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
		Schema::dropIfExists($prefix . 'agent_relation');
	}
}