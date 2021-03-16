<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use Illuminate\Console\Command;
use DB;

class ImportSuitMenusCommand extends Command
{
	protected $signature = 'import:suit-menu';

	protected $description = 'import suit menu';

	public function handle()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');
		$topMenu   = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();
		$parent    = DB::table(config('admin.database.menu_table'))->where('title', '促销管理')->where('parent_id', $topMenu->id)->first();
		$suit      = DB::table(config('admin.database.menu_table'))->where('title', '套餐管理')->where('parent_id', $parent->id)->first();
		if (!$suit) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $parent->id,
				'order'      => $lastOrder++,
				'title'      => '套餐管理',
				'icon'       => '',
				'blank'      => 1,
				'uri'        => 'store/promotion/suit',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}
}