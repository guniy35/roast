<?php

/*
 * This file is part of ibrand/discount.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Discount\Repositories\Eloquent;

use Carbon\Carbon;
use GuoJiangClub\Component\Discount\Models\Coupon;
use GuoJiangClub\Component\Discount\Models\Discount;
use GuoJiangClub\Component\Discount\Repositories\DiscountRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class DiscountRepositoryEloquent extends BaseRepository implements DiscountRepository
{
	/**
	 * Specify Model class name.
	 *
	 * @return string
	 */
	public function model()
	{
		return Discount::class;
	}

	/**
	 * get active discount.
	 *
	 * @param int $isCoupon 0:discount 1:coupon 2:all
	 *
	 * @return mixed
	 */
	public function findActive($isCoupon = 0)
	{
		$query = $this->model->where('status', 1);

		if (2 != $isCoupon) {
			$query = $query->where('coupon_based', $isCoupon);
		}

		return $query
			->where(function ($query) {
				$query->whereNull('starts_at')
					->orWhere(function ($query) {
						$query->where('starts_at', '<', Carbon::now());
					});
			})
			->where(function ($query) {
				$query->whereNull('ends_at')
					->orWhere(function ($query) {
						$query->where('ends_at', '>', Carbon::now());
					});
			})->with('rules', 'actions')->get();
	}

	/**
	 * 获取可领取优惠券列表/促销优惠活动.
	 *
	 * @param int $is_coupon
	 * @param int $channel
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function getDiscountByType($is_coupon = 1, $channel = 'ec', $limit = 10, $is_agent_share = null)
	{
		//->where('channel', $channel)
		$query = $this->model->where('status', 1)->where('coupon_based', $is_coupon);

		/*if ($is_agent_share) {
			$query = $query->where('is_agent_share', 1);
			->whereHas('hasAgents', function ($query) use ($is_agent_share) {
				$query->where('agent_id', $is_agent_share->id)
					->orWhere('agent_id', 0);
			})
		} else {
			$query = $query->where('is_open', 1);
		}*/

		$query = $query
			->where(function ($query) {
				$query->whereNull('starts_at')
					->orWhere(function ($query) {
						$query->where('starts_at', '<', Carbon::now());
					});
			})
			->where(function ($query) {
				$query->whereNull('ends_at')
					->orWhere(function ($query) {
						$query->where('ends_at', '>', Carbon::now());
					});
			})
			->with('rules', 'actions');

		return $query->paginate($limit);
	}

	public function getCouponByCodeAndUserID($coupon_code, $user_id)
	{

		$query = $this->model->where('status', 1)->where('coupon_based', 1)->where('code', $coupon_code);

		$data = $query
			->where(function ($query) {
				$query->whereNull('starts_at')
					->orWhere(function ($query) {
						$query->where('starts_at', '<', Carbon::now());
					});
			})
			->where(function ($query) {
				$query->whereNull('ends_at')
					->orWhere(function ($query) {
						$query->where('ends_at', '>', Carbon::now());
					});
			})
			->first();

		if (!$data) {
			return null;
		}

		$coupon_data = Coupon::where('discount_id', $data->id);

		$coupon = $coupon_data->where('user_id', $user_id)->count();

		$coupons = $coupon_data->count();

		if ($coupon >= $data->per_usage_limit) {

			$data->has_get = true;
		}

		if ($data->usage_limit <= 0 || $coupons >= $data->usage_limit) {

			$data->has_max = true;
		}

		return $data;
	}
}
