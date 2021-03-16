<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use Illuminate\Console\Command;
use DB;

class ImportPointMallMenus extends Command
{
	protected $signature = 'import:point-mall-menus';

	protected $description = 'import point mall menus';

	public function handle()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');
		$topMenu   = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();
		$mall      = DB::table(config('admin.database.menu_table'))->where('title', '积分商城')->where('parent_id', $topMenu->id)->first();
		if (!$mall) {
			$mallId = DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $topMenu->id,
				'order'      => ++$lastOrder,
				'title'      => '积分商城',
				'icon'       => 'fa-shopping-cart',
				'blank'      => 1,
				'uri'        => '',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		} else {
			$mallId = $mall->id;
		}

		$goods = DB::table(config('admin.database.menu_table'))->where('title', '积分商品管理')->where('parent_id', $mallId)->first();
		if (!$goods) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $mallId,
				'order'      => $lastOrder++,
				'title'      => '积分商品管理',
				'icon'       => '',
				'blank'      => 1,
				'uri'        => 'store/point-mall/goods',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$order = DB::table(config('admin.database.menu_table'))->where('title', '积分订单管理')->where('parent_id', $mallId)->first();
		if (!$order) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $mallId,
				'order'      => $lastOrder++,
				'title'      => '积分订单管理',
				'icon'       => '',
				'blank'      => 1,
				'uri'        => 'store/point-mall/orders?status=all',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}