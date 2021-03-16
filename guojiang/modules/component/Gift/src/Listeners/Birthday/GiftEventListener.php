<?php

namespace GuoJiangClub\Component\Gift\Listeners\Birthday;

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
            'gift.birthday.coupon',
            'GuoJiangClub\Component\Gift\Listeners\Birthday\CouponEventListener'
        );

        $events->listen(
            'gift.birthday.point',
            'GuoJiangClub\Component\Gift\Listeners\Birthday\PointEventListener'
        );

    }
}