<?php

/*
 * This file is part of ibrand/discount.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Discount\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DiscountRepository.
 */
interface DiscountRepository extends RepositoryInterface
{
    /**
     * @param int $isCoupon
     *
     * @return mixed
     */
    public function findActive($isCoupon = 0);

    /**
     * 获取可领取优惠券列表/促销优惠活动.
     *
     * @param int $is_coupon
     * @param int $channel
     * @param int $limit
     *
     * @return mixed
     */
    public function getDiscountByType($is_coupon = 1, $channel = 'ec', $limit = 10, $is_agent_share = null);

	/**
	 * 通过coupon_code获取用户有效优惠券信息
	 * @param $coupon_code
	 * @param $user_id
	 * @return mixed
	 */
	public function getCouponByCodeAndUserID($coupon_code, $user_id);
}
