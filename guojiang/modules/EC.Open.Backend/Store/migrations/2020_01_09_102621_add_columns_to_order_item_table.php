<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    $prefix = config('ibrand.app.database.prefix', 'ibrand_');
	    if (!Schema::hasColumn($prefix . 'order_item', 'divide_order_discount')) {
		    Schema::table($prefix . 'order_item', function (Blueprint $table) {
			    $table->integer('divide_order_discount')->default(0)->after('adjustments_total');
		    });
	    }

	    if (!Schema::hasColumn($prefix . 'order_item', 'shipping_id')) {
		    Schema::table($prefix . 'order_item', function (Blueprint $table) {
			    $table->integer('shipping_id')->default(0)->after('total');
		    });
	    }

	    if (!Schema::hasColumn($prefix . 'order_item', 'is_send')) {
		    Schema::table($prefix . 'order_item', function (Blueprint $table) {
			    $table->integer('is_send')->default(0)->after('shipping_id');
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
        //
    }
}
