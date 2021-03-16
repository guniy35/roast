<?php

namespace GuoJiangClub\Discover\Backend\Console;

use Illuminate\Console\Command;
use DB;

class ImportBackendMenus extends Command
{
	protected $signature = 'import:discover-menus';

	protected $description = 'import discover an backend menus';

	public function handle()
	{
		$lastOrder = DB::table(config('admin.database.menu_table'))->max('order');
		$topMenu   = DB::table(config('admin.database.menu_table'))->where('title', '商城管理')->where('parent_id', 0)->first();

		$topMenuId = $topMenu->id;

		$discover = DB::table(config('admin.database.menu_table'))->where('title', '发现')->where('parent_id', $topMenuId)->first();
		if (!$discover) {
			$discoverId = DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $topMenuId,
				'order'      => ++$lastOrder,
				'title'      => '发现',
				'icon'       => 'fa-angle-double-right',
				'blank'      => 1,
				'uri'        => '',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		} else {
			$discoverId = $discover->id;
		}

		$category = DB::table(config('admin.database.menu_table'))->where('title', '分类')->where('parent_id', $discoverId)->first();
		if (!$category) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $discoverId,
				'order'      => ++$lastOrder,
				'title'      => '分类',
				'icon'       => 'fa-calculator',
				'blank'      => 1,
				'uri'        => 'store/discover/category',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$tag = DB::table(config('admin.database.menu_table'))->where('title', '标签')->where('parent_id', $discoverId)->first();
		if (!$tag) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $discoverId,
				'order'      => ++$lastOrder,
				'title'      => '标签',
				'icon'       => 'fa-tags',
				'blank'      => 1,
				'uri'        => 'store/discover/tag',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$banner = DB::table(config('admin.database.menu_table'))->where('title', '轮播图')->where('parent_id', $discoverId)->first();
		if (!$banner) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $discoverId,
				'order'      => ++$lastOrder,
				'title'      => '轮播图',
				'icon'       => 'fa-image',
				'blank'      => 1,
				'uri'        => 'store/discover/banner',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}

		$content = DB::table(config('admin.database.menu_table'))->where('title', '内容')->where('parent_id', $discoverId)->first();
		if (!$content) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $discoverId,
				'order'      => ++$lastOrder,
				'title'      => '内容',
				'icon'       => 'fa-copy',
				'blank'      => 1,
				'uri'        => 'store/discover/content',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}
		/*$memberTopMenu = DB::table(config('admin.database.menu_table'))->where('title', '会员管理')->where('parent_id', 0)->first();

		$memberTopMenuId = $memberTopMenu->id;
		$privilege       = DB::table(config('admin.database.menu_table'))->where('title', '会员权益')->where('parent_id', $memberTopMenuId)->first();
		if (!$privilege) {
			DB::table(config('admin.database.menu_table'))->insertGetId([
				'parent_id'  => $memberTopMenuId,
				'order'      => ++$lastOrder,
				'title'      => '会员权益',
				'icon'       => 'fa-archive',
				'blank'      => 1,
				'uri'        => 'member/privilege',
				'created_at' => date('Y-m-d H:i:s', time()),
				'updated_at' => date('Y-m-d H:i:s', time()),
			]);
		}*/
	}
}