<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use iBrand\Common\Controllers\Controller;
/*use iBrand\Discover\Core\Models\MemberPrivilege;*/

class PrivilegeController extends Controller
{
	/*public function list()
	{
		$list = MemberPrivilege::where('status', 1)->get();

		return $this->success($list);
	}*/

	public function agreement()
	{
		$agreement = settings('user_agreement');

		return $this->success(['user_agreement' => $agreement]);
	}

	public function imgList()
	{
		return $this->success([
			['img' => 'https://cdn.ibrand.cc/P5.png', 'url' => '/pages/store/detail/detail?id=171'],
		]);
	}
}