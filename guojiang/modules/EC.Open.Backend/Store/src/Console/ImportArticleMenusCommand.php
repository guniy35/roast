<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use Illuminate\Console\Command;
use DB;

class ImportArticleMenusCommand extends Command
{
	protected $signature = 'import:add-article-menus';

	protected $description = 'import add-article menus';

	public function handle()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');

		$topMenu = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();

		if (!$topMenu) {
			$this->info('store management menu does not exist');
		} else {
			$topMenuId = $topMenu->id;
		}

		$article = DB::table(config('admin.database.menu_table'))->where('title', '文章管理')->where('parent_id', $topMenuId)->first();
		if (!$article) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $topMenuId,
				'order'      => ++$lastOrder,
				'title'      => '文章管理',
				'icon'       => 'fa-bar-chart',
				'blank'      => 1,
				'uri'        => 'store/article',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$agreement = DB::table(config('admin.database.menu_table'))->where('title', '用户协议')->where('parent_id', $topMenuId)->first();
		if (!$agreement) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $topMenuId,
				'order'      => ++$lastOrder,
				'title'      => '用户协议',
				'icon'       => 'fa-instagram',
				'blank'      => 1,
				'uri'        => 'store/setting/user/agreement',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
	}

}