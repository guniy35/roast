<?php

namespace GuoJiangClub\Component\Gift\Listeners\Birthday;

use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Gift\Models\GiftActivity;
use GuoJiangClub\Component\Gift\Repositories\CardRepository;
use Carbon\Carbon;

class PointEventListener
{
    private $pointRepository;
    private $giftActivityRepository;
    protected $cardRepository;

    public function __construct(PointRepository $pointRepository, GiftActivityRepository $giftActivityRepository, CardRepository $cardRepository

    )
    {
        $this->pointRepository = $pointRepository;
        $this->giftActivityRepository = $giftActivityRepository;
        $this->cardRepository = $cardRepository;
    }

    public function handle(User $user, $activity)
    {

        if ($activity) {
            if (
//                settings('point_enabled') &&
                $activity->point && !$activity->is_receive) {
                $this->AutoGivePoint($user, $activity);
            }
        }
    }


    public function AutoGivePoint($user, $activity)
    {
        try {
            $point_status = false;
            $time = Carbon::now()->timestamp;
            $birthday = date('Y-m-d', $time);
            $point = $this->pointRepository->orderBy('created_at', 'desc')->findWhere(['action' => 'gift_birthday_point', 'user_id' => $user->id])->first();
            if ($point) {
                if (intval(strtotime(date('Y-m-d', strtotime($point->created_at))) !== intval(strtotime($birthday)))) {
                    $point_status = true;
                }
            }
            if ($point == null || $point_status) {
                $this->pointRepository->create(['user_id' => $user->id, 'action' =>
                    'gift_birthday_point', 'note' => date('Y', $time) . '年生日礼赠送积分', 'item_type' => GiftActivity::class,
                    'item_id' => $activity->id
                    , 'value' => $activity->point]);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
        }


    }


}