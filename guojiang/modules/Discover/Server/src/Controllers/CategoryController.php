<?php

namespace GuoJiangClub\Discover\Server\Controllers;

use iBrand\Common\Controllers\Controller;
use GuoJiangClub\Component\Category\Category;

class CategoryController extends Controller
{
	public function index()
	{
		$list = Category::where('is_front_show', 1)->where('status', 1)->get(['id', 'name']);

		return $this->success($list);
	}
}