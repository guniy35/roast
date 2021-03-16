<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		/*用户签到活动*/
		if (!Schema::hasTable($prefix . 'marketing_sign')) {
			Schema::create($prefix . 'marketing_sign', function (Blueprint $table) {
				$table->increments('id');
				$table->string('title')->comment('活动名称');
				$table->string('label')->nullable()->comment('活动说明');
				$table->string('share_text')->nullable()->comment('分享文案');
				$table->text('rules')->nullable()->comment('连续签到规则：{"1","2"...},暂时限定顶多7天');
				$table->text('action')->comment('连续签到动作，送积分/送优惠券： [{"point":1,"coupon":2},{"point":2,"coupon":2}]');
				$table->integer('status')->default(1)->comment('状态：1 有效 ，0 失效');
				$table->timestamps();
			});
		}
		if (!Schema::hasTable($prefix . 'marketing_sign_item')) {
			Schema::create($prefix . 'marketing_sign_item', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('sign_id');
				$table->integer('user_id');
				$table->text('sign_reward')->nullable()->comment('本次签到的抽奖列表："[{"reward_id":1,"label":"2\u5143\u4f18\u60e0\u5238","coupon_id":12,"status":true},{"reward_id":1,"label":"2\u5143\u4f18\u60e0\u5238","coupon_id":12}]"');
				$table->timestamp('sign_time')->comment('签到时间');
				$table->integer('days')->default(1)->comment('连续第几天签到');
				$table->tinyInteger('reward_status')->default(0)->comment('是否已抽奖');
				$table->integer('reward_id')->default(0)->comment('中奖的ID');
				$table->timestamps();
			});
		}

		if (!Schema::hasTable($prefix . 'marketing_sign_reward')) {
			Schema::create($prefix . 'marketing_sign_reward', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('sign_id');
				$table->string('label')->comment('奖品提示');
				$table->string('type')->comment('奖品类型：point 积分；coupon 优惠券；luck 谢谢参与');
				$table->integer('type_value')->default(0)->comment('如果是积分，则是积分值；优惠券为discountID');
				$table->timestamps();
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
		Schema::dropIfExists($prefix . 'marketing_sign');
		Schema::dropIfExists($prefix . 'marketing_sign_item');
		Schema::dropIfExists($prefix . 'marketing_sign_reward');
	}
}
