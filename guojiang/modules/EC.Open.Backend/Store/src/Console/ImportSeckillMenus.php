<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use Illuminate\Console\Command;
use DB;

class ImportSeckillMenus extends Command
{
	protected $signature = 'import:seckill-menus';

	protected $description = 'import seckill menus';

	public function handle()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');
		$topMenu   = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();
		$parent    = DB::table(config('admin.database.menu_table'))->where('title', '促销管理')->where('parent_id', $topMenu->id)->first();
		$seckill   = DB::table(config('admin.database.menu_table'))->where('title', '秒杀管理')->where('parent_id', $parent->id)->first();
		if (!$seckill) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent->id,
				'order'      => $lastOrder++,
				'title'      => '秒杀管理',
				'icon'       => '',
				'blank'      => 1,
				'uri'        => 'store/promotion/seckill',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}