<?php

namespace GuoJiangClub\Component\Gift\Listeners\NewUser;

use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Gift\Models\GiftActivity;

class PointEventListener
{
    private $pointRepository;
    private $giftActivityRepository;

    public function __construct(PointRepository $pointRepository, GiftActivityRepository $giftActivityRepository

    )
    {
        $this->pointRepository = $pointRepository;
        $this->giftActivityRepository = $giftActivityRepository;
    }

    public function handle(User $user, $activity)
    {
        if ($activity) {
            if (
//                settings('point_enabled') &&
                $activity->point && $activity->is_new_user && !$activity->is_receive) {
                $this->AutoGivePoint($user, $activity);
            }
        }
    }


    public function AutoGivePoint($user, $activity)
    {

        try {
            if (!$this->pointRepository->findWhere(['action' => 'gift_new_user_point', 'user_id' => $user->id])->first()) {
                $this->pointRepository->create(['user_id' => $user->id, 'action' =>
                    'gift_new_user_point', 'note' => '新人礼赠送积分', 'item_type' => GiftActivity::class,
                    'item_id' => $activity->id
                    , 'value' => $activity->point]);

            }

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
        }


    }


}