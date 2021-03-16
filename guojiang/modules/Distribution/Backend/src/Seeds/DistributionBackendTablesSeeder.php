<?php

namespace GuoJiangClub\Distribution\Backend\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistributionBackendTablesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');

		$parent = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => 0,
			'order'      => $lastOrder++,
			'title'      => '分销管理',
			'icon'       => 'iconfont icon-fenxiaoguanli',
			'blank'      => 1,
			'uri'        => 'distribution',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$distribution_managemen = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '分销设置',
			'icon'       => 'iconfont icon-fenxiaoshezhi',
			'blank'      => 1,
			'uri'        => '',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $distribution_managemen,
			'order'      => $lastOrder++,
			'title'      => '系统设置',
			'icon'       => '',
			'blank'      => 1,
			'uri'        => 'distribution/setting/sys_setting',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $distribution_managemen,
			'order'      => $lastOrder++,
			'title'      => '商品设置',
			'icon'       => '',
			'blank'      => 1,
			'uri'        => 'distribution/setting/goods?status=ACTIVITY',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$distributor_managemen = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '分销员管理',
			'icon'       => 'iconfont icon-fenxiaoyuanguanli',
			'blank'      => 1,
			'uri'        => '',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $distributor_managemen,
			'order'      => $lastOrder++,
			'title'      => '分销员列表',
			'icon'       => '',
			'blank'      => 1,
			'uri'        => 'distribution/agent?status=STATUS_AUDITED',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $distributor_managemen,
			'order'      => $lastOrder++,
			'title'      => '佣金提现管理',
			'icon'       => '',
			'blank'      => 1,
			'uri'        => 'distribution/cash?status=STATUS_AUDIT',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		$distributor_order_managemen = DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $parent,
			'order'      => $lastOrder++,
			'title'      => '分销订单管理',
			'icon'       => 'iconfont icon-fenxiaodingdan',
			'blank'      => 1,
			'uri'        => '',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);

		DB::table(config('admin.database.menu_table'))->insertGetId([
			'parent_id'  => $distributor_order_managemen,
			'order'      => $lastOrder++,
			'title'      => '订单列表',
			'icon'       => '',
			'blank'      => 1,
			'uri'        => 'distribution/orders?status=ALL',
			'created_at' => date('Y-m-d H:i:s', time()),
			'updated_at' => date('Y-m-d H:i:s', time()),
		]);
	}
}
