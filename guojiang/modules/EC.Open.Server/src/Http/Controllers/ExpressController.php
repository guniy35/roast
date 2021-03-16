<?php

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;

use Express;

class ExpressController extends Controller
{
	public function query()
	{
		$number = request('no');

		$result = Express::query($number);
		if (!empty($result) && is_array($result)) {
			$first = array_first($result);

			return $this->success($first['result']);
		}

		return $this->failed('快递信息查询失败');
	}
}