<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use Illuminate\Console\Command;
use DB;

class ImportGoodsLimitMenus extends Command
{
	protected $signature = 'import:goods-limit-menus';

	protected $description = 'import goods limit menus';

	public function handle()
	{
		$lastOrder  = DB::table(config('admin.database.menu_table'))->max('order');
		$topMenu    = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();
		$parent     = DB::table(config('admin.database.menu_table'))->where('title', '商品管理')->where('parent_id', $topMenu->id)->first();
		$goodsLimit = DB::table(config('admin.database.menu_table'))->where('title', '商品限购')->where('parent_id', $parent->id)->first();
		if (!$goodsLimit) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent->id,
				'order'      => $lastOrder++,
				'title'      => '商品限购',
				'icon'       => '',
				'blank'      => 1,
				'uri'        => 'store/goods/limit?status=ACTIVITY',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}