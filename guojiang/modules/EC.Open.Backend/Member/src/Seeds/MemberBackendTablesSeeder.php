<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) GuoJiangClub <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Backend\Member\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberBackendTablesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');

		$topMenu = DB::table(config('admin.database.menu_table'))->where('title', '会员管理')->where('parent_id', 0)->first();
		if ($topMenu) {
			$parent = $topMenu->id;
		} else {
			$parent = DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => 0,
				'order'      => $lastOrder++,
				'title'      => '会员管理',
				'icon'       => 'iconfont icon-huiyuanguanli-',
				'blank'      => 1,
				'uri'        => 'member/users',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$users = DB::table(config('admin.database.menu_table'))->where('title', '会员管理')->where('parent_id', $parent)->first();
		if (!$users) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent,
				'order'      => $lastOrder++,
				'title'      => '会员管理',
				'icon'       => 'iconfont icon-huiyuanguanli--',
				'blank'      => 1,
				'uri'        => 'member/users',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$points = DB::table(config('admin.database.menu_table'))->where('title', '会员积分记录')->where('parent_id', $parent)->first();
		if (!$points) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent,
				'order'      => $lastOrder++,
				'title'      => '会员积分记录',
				'icon'       => 'iconfont icon-huiyuanjifenjilu',
				'blank'      => 1,
				'uri'        => 'member/points',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$balances = DB::table(config('admin.database.menu_table'))->where('title', '会员余额记录')->where('parent_id', $parent)->first();
		if (!$balances) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent,
				'order'      => $lastOrder++,
				'title'      => '会员余额记录',
				'icon'       => 'iconfont icon-huiyuanjifenjilu',
				'blank'      => 1,
				'uri'        => 'member/balances',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$recharge = DB::table(config('admin.database.menu_table'))->where('title', '储值管理')->where('parent_id', $parent)->first();
		if (!$recharge) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent,
				'order'      => $lastOrder++,
				'title'      => '储值管理',
				'icon'       => 'iconfont icon-zhifushezhi',
				'blank'      => 1,
				'uri'        => 'member/recharge',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}
