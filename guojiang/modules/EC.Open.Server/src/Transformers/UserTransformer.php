<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Transformers;

use DB;

class UserTransformer extends BaseTransformer
{
	public static $excludeable = [
		'password',
		'confirmation_code',
		'remember_token',
	];

	public function transformData($model)
	{
		$user = array_except($model->toArray(), self::$excludeable);

		$user['user_info_fill'] = 1;
		if (!$user['avatar'] && !$user['nick_name']) {
			$user['user_info_fill'] = 0;
		}

		$user['is_agent']                    = 0;
		$distribution_status                 = settings('distribution_status');
		$distribution_recruit_status         = settings('distribution_recruit_status');
		$user['distribution_status']         = $distribution_status;
		$user['distribution_recruit_status'] = $distribution_recruit_status;

		if ($distribution_status) {
			$prefix = config('ibrand.app.database.prefix', 'ibrand_');
			$check  = DB::table($prefix . 'agent')->where('user_id', $user['id'])->first();
			if ($check) {
				//状态  1：审核通过 2：审核不通过 3：清退 4:待审核
				$user['is_agent'] = 0 == $check->status ? 4 : $check->status;
			}
		}

		return $user;
	}
}
