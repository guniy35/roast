<?php

namespace GuoJiangClub\Component\Gift\Processor;

use GuoJiangClub\Component\Gift\Services\DirectionalCouponService;
use GuoJiangClub\Component\Gift\Models\GiftCouponReceive;
use GuoJiangClub\Component\Gift\Repositories\GiftDirectionalCouponRepository;
use GuoJiangClub\Component\Gift\Models\GiftDirectionalCoupon;
use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use DB;


class DirectionalCouponProcessor
{
    protected $couponRepository;
    protected $directionalCouponService;
    protected $giftDirectionalCouponRepository;


    public function __construct(
        DirectionalCouponService $DirectionalCouponService, CouponRepository $couponRepository, GiftDirectionalCouponRepository $giftDirectionalCouponRepository
    )
    {
        $this->directionalCouponService = $DirectionalCouponService;
        $this->couponRepository = $couponRepository;
        $this->giftDirectionalCouponRepository = $giftDirectionalCouponRepository;
    }


    public function DirectionalCoupon($gift, $user_id)
    {

        if (count($user_id) > 0) {
            foreach ($user_id as $item) {
                try {
                    DB::beginTransaction();
                    if ($gift = GiftDirectionalCoupon::where('status', 1)->find($gift->id)) {
                        $num = GiftCouponReceive::where('user_id', $item)->where('type', 'gift_directional_coupon')->where('discount_id', $gift->coupon_id)->where('type_id', $gift->id)->count();
                        if (!$num) {
                            if ($couponConvert = $this->couponRepository->getCouponsByUser($user_id, $gift->coupon_id)) {
                                GiftCouponReceive::create(['type_id' => $gift->id, 'discount_id' => $gift->coupon_id, 'user_id' => $item, 'type' => 'gift_directional_coupon']);
                            }
                        }
                    } else {
                        break;
                    }
                    DB::commit();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    \Log::info($exception->getMessage());
                }
            }
        }

    }


}