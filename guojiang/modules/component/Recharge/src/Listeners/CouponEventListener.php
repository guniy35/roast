<?php

namespace GuoJiangClub\Component\Recharge\Listeners;

use GuoJiangClub\Component\Balance\BalanceOrder;
use GuoJiangClub\Component\Recharge\Repositories\RechargeRuleRepository;
use GuoJiangClub\Component\Recharge\Models\GiftCouponReceive;
use GuoJiangClub\EC\Open\Core\Services\DiscountService;
use DB;

class CouponEventListener
{

	private   $rechargeRuleRepository;
	protected $discountService;

	public function __construct(RechargeRuleRepository $rechargeRuleRepository, DiscountService $discountService)
	{

		$this->rechargeRuleRepository = $rechargeRuleRepository;
		$this->discountService        = $discountService;
	}

	public function handle(BalanceOrder $order)
	{
		if ($recharge = $this->rechargeRuleRepository->getRechargeByID($order->recharge_rule_id)) {
			if ($recharge->open_coupon && $order->pay_status) {
				$this->AutoGiveCoupon($order, $recharge);
			}
		}
	}

	public function AutoGiveCoupon($order, $recharge)
	{
		try {
			if ($recharge->open_coupon && count($recharge->gift) > 0) {
				foreach ($recharge->gift as $item) {
					$num = GiftCouponReceive::where('user_id', $order->user_id)->where('balance_order_id', $order->id)->where('discount_id', $item->coupon->id)->where('gift_coupon_id', $item->id)->get();
					if ($item->num > count($num)) {
						DB::beginTransaction();
						if ($couponConvert = $this->discountService->getCouponConvert($item->coupon->code, $order->user_id)) {
							GiftCouponReceive::create(['balance_order_id' => $order->id, 'discount_id' => $item->coupon->id, 'user_id' => $order->user_id, 'type' => 'gift_recharge', 'gift_coupon_id' => $item->id]);
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