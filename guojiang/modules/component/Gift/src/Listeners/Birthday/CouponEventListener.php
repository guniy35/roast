<?php

namespace GuoJiangClub\Component\Gift\Listeners\Birthday;


use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Gift\Models\GiftCouponReceive;
use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use GuoJiangClub\Component\Gift\Repositories\CardRepository;
use Carbon\Carbon;
use DB;

class CouponEventListener
{

    private $giftActivityRepository;
    protected $couponRepository;
    protected $cardRepository;

    public function __construct(GiftActivityRepository $giftActivityRepository, CouponRepository $couponRepository, CardRepository $cardRepository)
    {

        $this->giftActivityRepository = $giftActivityRepository;
        $this->couponRepository = $couponRepository;
        $this->cardRepository = $cardRepository;
    }

    public function handle(User $user, $activity)
    {
        if ($activity) {
            if ($activity->open_coupon && !$activity->is_receive) {
                $this->AutoGiveCoupon($user, $activity);
            }
        }
    }

    public function AutoGiveCoupon($user, $activity)
    {
        try {
            $time = Carbon::now()->timestamp;
            $date = date('Y-', $time);
            if (count($activity->gift) > 0) {
                foreach ($activity->gift as $item) {
                    $num = GiftCouponReceive::where('user_id', $user->id)->where('type', 'gift_birthday')
                        ->where('discount_id', $item->coupon->id)->where('gift_coupon_id', $item->id)
                        ->where('type_id', $activity->id)->where('created_at', "$date%")->get();
                    if ($item->num > count($num)) {
                        DB::beginTransaction();
                        //发优惠券
                        if ($couponConvert = $this->couponRepository->getCouponsByUser($user->id, $item->coupon->id)) {
                            GiftCouponReceive::create(['type_id' => $activity->id, 'discount_id' => $item->coupon->id, 'user_id' => $user->id, 'type' => 'gift_birthday', 'gift_coupon_id' => $item->id]);
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