<?php

namespace GuoJiangClub\Component\Gift\Listeners\DirectionalCoupon;

class GiftEventListener
{

    public function __construct()
    {
    }

    public function subscribe($events)
    {
        $events->listen(
            'directional.coupon',
            'GuoJiangClub\Component\Gift\Listeners\DirectionalCoupon\CouponEventListener'
        );

    }
}