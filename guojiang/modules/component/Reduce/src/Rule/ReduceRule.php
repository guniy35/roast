<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Rule;

class ReduceRule
{
    /**
     * 可砍价的金额.
     *
     * @var float
     */
    protected $amount;

    /**
     * 总共可砍价的次数.
     *
     * @var int
     */
    protected $num;

    /**
     * 单次砍价最小金额.
     *
     * @var float
     */
    protected $min_amount;

    /**
     * 砍价分配结果.
     *
     * @var array
     */
    protected $items = [];

    public function getReduce($amount, $num = 1, $min_amount = 0.01)
    {
        $this->amount = $amount;

        $this->num = $num;

        $this->min_amount = $min_amount;

        // A. 验证
        if ($this->amount < $validAmount = $this->min_amount * $this->num) {
            return ['errormsg' => '可砍价的金额必须≥'.$validAmount.'元'];
        }

        // B. 分配
        $this->apportion();

        return [
            'errormsg' => '',
            'items' => $this->items,
        ];
    }

    /**
     * 分配.
     */
    protected function apportion()
    {
        $num = $this->num;  // 剩余可分配的个数
        $amount = $this->amount;  //剩余可领取的金额

        while ($num >= 1) {
            // 剩余一个的时候，直接取剩余
            if (1 == $num) {
                $coupon_amount = $this->decimal_number($amount);
            } else {
                $avg_amount = $this->decimal_number($amount / $num);  // 剩余的的平均金额

                $coupon_amount = $this->decimal_number(
                    $this->calcCouponAmount($avg_amount, $amount, $num)
                );
            }

            $this->items[] = $coupon_amount; // 追加分配

            $amount -= $coupon_amount;
            --$num;
        }

        shuffle($this->items);  //随机打乱
    }

    /**
     * 计算分配的金额.
     *
     * @param float $avg_amount 每次计算的平均金额
     * @param float $amount     剩余可领取金额
     * @param int   $num        剩余可领取的个数
     *
     * @return float
     */
    protected function calcCouponAmount($avg_amount, $amount, $num)
    {
        // 如果平均金额小于等于最低金额，则直接返回最低金额
        if ($avg_amount <= $this->min_amount) {
            return $this->min_amount;
        }

        // 浮动计算
        $coupon_amount = $this->decimal_number($avg_amount * (1 + $this->apportionRandRatio()));

        // 如果低于最低金额或超过可领取的最大金额，则重新获取
        if ($coupon_amount < $this->min_amount
            || $coupon_amount > $this->calcCouponAmountMax($amount, $num)
        ) {
            return $this->calcCouponAmount($avg_amount, $amount, $num);
        }

        return $coupon_amount;
    }

    /**
     * 计算分配的金额-可领取的最大金额.
     *
     * @param float $amount
     * @param int   $num
     */
    protected function calcCouponAmountMax($amount, $num)
    {
        return $this->min_amount + $amount - $num * $this->min_amount;
    }

    /**
     * 金额浮动比例.
     */
    protected function apportionRandRatio()
    {
        // 70%机率获取剩余平均值的大幅度（可能正数、可能负数）
        if (rand(1, 100) <= 70) {
            return rand(-70, 70) / 100; // 上下幅度70%
        }

        return rand(-30, 30) / 100; // 其他情况，上下浮动30%；
    }

    /**
     * 格式化金额，保留2位.
     *
     * @param float $amount
     *
     * @return float
     */
    protected function decimal_number($amount)
    {
        return sprintf('%01.2f', round($amount, 2));
    }
}
