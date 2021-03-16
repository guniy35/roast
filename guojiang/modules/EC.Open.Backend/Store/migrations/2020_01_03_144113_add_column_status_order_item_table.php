<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    $prefix = config('ibrand.app.database.prefix', 'ibrand_');
	    if (!Schema::hasColumn($prefix . 'order_item', 'status')) {
		    Schema::table($prefix . 'order_item', function (Blueprint $table) {
			    $table->tinyInteger('status')->default(1);
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
