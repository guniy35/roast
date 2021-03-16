<?php

/*
 * This file is part of ibrand/advert.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
	'models' => [
		'goods'       => GuoJiangClub\Component\Product\Models\Goods::class,
		'discount'    => GuoJiangClub\Component\Discount\Models\Discount::class,
		'category'    => GuoJiangClub\Component\Category\Category::class,
		'microPage'   => GuoJiangClub\Component\Advert\Models\MicroPage::class,
		'groupon'     => GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon::class,
		'seckillItem' => GuoJiangClub\Component\Seckill\Models\SeckillItem::class,
		'brand'       => GuoJiangClub\Component\Product\Brand::class,
		'article'     => GuoJiangClub\EC\Open\Backend\Store\Model\Article::class,
	],
	'type'   => [
		'store_detail'  => [
			'name' => '商品详情页',
			'page' => '/pages/store/detail/detail?id=',
		],
		'store_list'    => [
			'name' => '商品分类页',
			'page' => '/pages/store/list/list?c_id=',
		],
		'store_seckill' => [
			'name' => '秒杀列表页',
			'page' => '/pages/store/seckill/seckill',
		],
		'store_groups'  => [
			'name' => '拼团列表页',
			'page' => '/pages/store/groups/groups',
		],
		'store_reduce'  => [
			'name' => '砍价列表页',
			'page' => '/pages/bargain/index/index',
		],
		'other_micro'   => [
			'name' => '微页面',
			'page' => '/pages/index/microPages/microPages?id=',
		],
		'other_links'   => [
			'name' => '公众号文章',
			'page' => '/pages/other/links/links?url=',
		],
		'other'         => [
			'name' => '自定义',
			'page' => 'other',
		],
		'brand_detail'  => [
			'name' => '品牌详情页',
			'page' => '/pages/brand/detail/detail?id=',
		],
	],
	'meta'   => [
		'下边距' => [
			'name'  => 'margin_bottom',
			'value' => 0,
		],
		'左内距' => [
			'name'  => 'padding_left',
			'value' => 0,
		],
		'右内距' => [
			'name'  => 'padding_right',
			'value' => 0,
		],
		'下内距' => [
			'name'  => 'padding_bottom',
			'value' => 0,
		],
		'上内距' => [
			'name'  => 'padding_top',
			'value' => 0,
		],
		'背景色' => [
			'name'  => 'background_color',
			'value' => '#FFFFFF',
		],
	],
];
