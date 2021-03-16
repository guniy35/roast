<?php

namespace GuoJiangClub\Component\Gift\Listeners\NewUser;


use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Gift\Models\GiftCouponReceive;
use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use DB;

class CouponEventListener
{

    private $giftActivityRepository;
    protected $couponRepository;

    public function __construct(GiftActivityRepository $giftActivityRepository, CouponRepository $couponRepository)
    {

        $this->giftActivityRepository = $giftActivityRepository;
        $this->couponRepository = $couponRepository;
    }

    public function handle(User $user, $activity)
    {
        if ($activity) {
            if ($activity->open_coupon && $activity->is_new_user && !$activity->is_receive) {
                $this->AutoGiveCoupon($user, $activity);
            }
        }
    }


    public function AutoGiveCoupon($user, $activity)
    {
        try {
            if (count($activity->gift) > 0) {
                foreach ($activity->gift as $item) {
                    $num = GiftCouponReceive::where('user_id', $user->id)->where('type', 'gift_new_user')->where('discount_id', $item->coupon->id)->where('gift_coupon_id', $item->id)->where('type_id', $activity->id)->get();
                    if ($item->num > count($num)) {
                        DB::beginTransaction();
                        if ($couponConvert = $this->couponRepository->getCouponsByUser($user->id, $item->coupon->id)) {
                            GiftCouponReceive::create(['type_id' => $activity->id, 'discount_id' => $item->coupon->id, 'user_id' => $user->id, 'type' => 'gift_new_user', 'gift_coupon_id' => $item->id]);
                        }
                        DB::commit();
                    }
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
        }

    }


}