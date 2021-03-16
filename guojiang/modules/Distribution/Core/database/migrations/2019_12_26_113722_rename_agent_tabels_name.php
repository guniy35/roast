<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameAgentTabelsName extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (Schema::hasTable('el_agent') && !Schema::hasTable($prefix . 'agent') && 'el_agent' != $prefix . 'agent') {
			Schema::rename('el_agent', $prefix . 'agent');
		}

		if (Schema::hasTable('el_agent_cash') && !Schema::hasTable($prefix . 'agent_cash') && 'el_agent_cash' != $prefix . 'agent_cash') {
			Schema::rename('el_agent_cash', $prefix . 'agent_cash');
		}

		if (Schema::hasTable('el_agent_commission') && !Schema::hasTable($prefix . 'agent_commission') && 'el_agent_commission' != $prefix . 'agent_commission') {
			Schema::rename('el_agent_commission', $prefix . 'agent_commission');
		}

		if (Schema::hasTable('el_agent_goods') && !Schema::hasTable($prefix . 'agent_goods') && 'el_agent_goods' != $prefix . 'agent_goods') {
			Schema::rename('el_agent_goods', $prefix . 'agent_goods');
		}

		if (Schema::hasTable('el_agent_order') && !Schema::hasTable($prefix . 'agent_order') && 'el_agent_order' != $prefix . 'agent_order') {
			Schema::rename('el_agent_order', $prefix . 'agent_order');
		}

		if (Schema::hasTable('el_agent_order_item') && !Schema::hasTable($prefix . 'agent_order_item') && 'el_agent_order_item' != $prefix . 'agent_order_item') {
			Schema::rename('el_agent_order_item', $prefix . 'agent_order_item');
		}

		if (Schema::hasTable('el_agent_relation') && !Schema::hasTable($prefix . 'agent_relation') && 'el_agent_relation' != $prefix . 'agent_relation') {
			Schema::rename('el_agent_relation', $prefix . 'agent_relation');
		}

		if (Schema::hasTable('el_agent_user_relation') && !Schema::hasTable($prefix . 'agent_user_relation') && 'el_agent_user_relation' != $prefix . 'agent_user_relation') {
			Schema::rename('el_agent_user_relation', $prefix . 'agent_user_relation');
		}

		if (Schema::hasTable('el_agent_user_relation') && !Schema::hasTable($prefix . 'agent_user_relation') && 'el_agent_user_relation' != $prefix . 'agent_user_relation') {
			Schema::rename('el_agent_user_relation', $prefix . 'agent_user_relation');
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
