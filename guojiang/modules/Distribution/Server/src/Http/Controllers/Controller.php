<?php

namespace GuoJiangClub\Distribution\Server\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
	use Helpers;

	public function api($data = [], $status = true, $code = 200, $message = '')
	{
		return new Response(['status'    => $status
		                     , 'code'    => $code
		                     , 'message' => $message
		                     , 'data'    => empty($data) ? null : $data]);
	}

}