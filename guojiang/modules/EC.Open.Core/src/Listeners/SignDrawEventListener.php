<?php

namespace GuoJiangClub\EC\Open\Core\Listeners;

use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use GuoJiangClub\Component\Marketing\Models\SignItem;
use GuoJiangClub\Component\Marketing\Models\SignReward;
use GuoJiangClub\Component\Point\Repository\PointRepository;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/06/13
 * Time: 12:07
 */
class SignDrawEventListener
{
    protected $pointRepository;
    protected $couponRepository;

    public function __construct(PointRepository $pointRepository,
                                CouponRepository $couponRepository)
    {
        $this->pointRepository = $pointRepository;
        $this->couponRepository = $couponRepository;
    }


    /**
     * 签到事件
     * @param $user_id
     * @param $sign_item
     */
    public function onSign($user_id, $sign_item, $sign)
    {
        $action = $sign->action[array_search($sign_item->days, $sign->rules)];
        foreach ($action as $key => $item) {
            if ($key == 'point' AND $item) {
                $this->pointRepository->create(['user_id' => $user_id, 'action' =>
                    'marketing_sign', 'note' => '签到获得积分', 'item_type' => SignItem::class,
                    'item_id' => $sign_item->id
                    , 'value' => $item]);
                event('point.change', $user_id);
            }

            if ($key == 'coupon' AND $item) {
                $this->couponRepository->getCouponsByUser($user_id, $item);
            }
        }
    }

    /**
     * 抽奖事件
     * @param $reward_id
     * @param $sign_item_id
     * @param $user_id
     */
    public function onSignDraw($reward_id, $sign_item_id, $user_id)
    {
        $reward = SignReward::find($reward_id);
        if ($reward->type == 'point') {
            $this->pointRepository->create(['user_id' => $user_id, 'action' =>
                'marketing_sign_reward', 'note' => '签到抽奖获得积分', 'item_type' => SignItem::class,
                'item_id' => $sign_item_id
                , 'value' => $reward->type_value]);
            event('point.change', $user_id);
        }

        if ($reward->type == 'coupon') {
            $this->couponRepository->getCouponsByUser($user_id, $reward->type_value);
        }

    }


    public function subscribe($events)
    {
        $events->listen(
            'sign.draw',
            'GuoJiangClub\EC\Open\Core\Listeners\SignDrawEventListener@onSignDraw'
        );

        $events->listen(
            'sign.done',
            'GuoJiangClub\EC\Open\Core\Listeners\SignDrawEventListener@onSign'
        );
    }
}