<?php

namespace GuoJiangClub\Component\Gift\Listeners\NewUser;

use GuoJiangClub\Component\Point\Repository\PointRepository;

class GiftEventListener
{

    protected $pointRepository;

    public function __construct(PointRepository $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    public function subscribe($events)
    {
        $events->listen(
            'gift.new.user.point',
            'GuoJiangClub\Component\Gift\Listeners\NewUser\PointEventListener'
        );

        $events->listen(
            'gift.new.user.coupon',
            'GuoJiangClub\Component\Gift\Listeners\NewUser\CouponEventListener'
        );

    }
}