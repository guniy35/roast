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
use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CouponRepositoryEloquent.
 */
class CouponRepositoryEloquent extends BaseRepository implements CouponRepository
{
	/**
	 * Specify Model class name.
	 *
	 * @return string
	 */
	public function model()
	{
		return Coupon::class;
	}

	/**
	 * @param        $userId
	 * @param int    $paginate
	 * @param string $channel
	 *
	 * @return mixed
	 */
	public function findActiveByUser($userId, $paginate = 15)
	{
		$res = $this->model->where('user_id', $userId)->whereNull('used_at')
			->where(function ($query) {
				$query->whereNull('expires_at')
					->orWhere(function ($query) {
						$query->where('expires_at', '>', Carbon::now());
					});
			})
			->with('discount', 'discount.rules', 'discount.actions')
			->whereHas('discount', function ($query) {
				$query->where(function ($query) {
					$query->whereNull('useend_at')
						->orWhere(function ($query) {
							$query->where('useend_at', '>', Carbon::now());
						});
				})->where('status', 1);
			});

		if (!$paginate) {
			return $res->get();
		}

		return $res->paginate($paginate);
	}

	/**
	 * @param     $userId
	 * @param int $paginate
	 *
	 * @return mixed
	 */
	public function findInvalidByUser($userId, $paginate = 15)
	{
		return $this->model->where('user_id', $userId)->whereNull('used_at')
			->with('discount', 'discount.rules', 'discount.actions')
			->where(function ($query) {
				$query->where(function ($query) {
					$query->whereNotNull('expires_at')->where('expires_at', '<=', Carbon::now());
				})
					->orWhere(function ($query) {
						$query->whereHas('discount', function ($query) {
							$query->where('status', 0)->orWhere('useend_at', '<=', Carbon::now());
						});
					});
			})->paginate($paginate);
	}

	/**
	 * 获取已使用的优惠券.
	 *
	 * @param     $userId
	 * @param int $paginate
	 *
	 * @return mixed
	 */
	public function findUsedByUser($userId, $paginate = 15)
	{
		return $this->model->where('user_id', $userId)->whereNotNull('used_at')
			->with('discount', 'discount.rules', 'discount.actions')
			->paginate($paginate);
	}

	public function getCouponsByUser($user_id, $coupon_id)
	{
		$discount = Discount::find($coupon_id);

		if (!isset($discount->usage_limit) || $discount->usage_limit < 1) {
			return false;
		}

		if ($discount->usage_limit - 1 <= 0) {
			return false;
		}

		$count = Coupon::where('user_id', $user_id)->where('discount_id', $coupon_id)->get()->count();
		if ($count >= $discount->per_usage_limit) {
			return false;
		}

		$input['user_id']     = $user_id;
		$input['discount_id'] = $coupon_id;

		if ($discount->useend_at) {
			$input['expires_at'] = $discount->useend_at;
		} else {
			$input['expires_at'] = $discount->ends_at;
		}

		$input['code'] = build_order_no('C');

		$coupon = $this->create($input);

		if ($coupon) {

			$decrementDiscount = Discount::where(['id' => $coupon_id])->decrement('usage_limit');

			$incrementDisount = Discount::where(['id' => $coupon_id])->increment('used');

			if ($decrementDiscount AND $incrementDisount) {

				return $coupon;
			}
		}

		return false;
	}

	public function getCouponsByUserID($user_id, $coupon_id, $utmCampaign = null, $utmSource = null)
	{
		$discount = Discount::find($coupon_id);

		if (!isset($discount->usage_limit) || $discount->usage_limit < 1) {
			return false;
		}

		//$input['channel']     = $discount->channel;
		$input['user_id']     = $user_id;
		$input['discount_id'] = $coupon_id;

		if ($discount->useend_at) {
			$input['expires_at'] = $discount->useend_at;
		} else {
			$input['expires_at'] = $discount->ends_at;
		}

		$input['code'] = build_order_no('C');

		if ($utmCampaign) {
			$input['utm_campaign'] = $utmCampaign;
		}

		if ($utmSource) {
			$input['utm_source'] = $utmSource;
		}

		$coupon = $this->create($input);

		if ($coupon) {

			$decrementDiscount = Discount::where(['id' => $coupon_id])->decrement('usage_limit');

			$incrementDisount = Discount::where(['id' => $coupon_id])->increment('used');

			if ($decrementDiscount AND $incrementDisount) {

				return $coupon;
			}
		}

		return false;
	}
}
