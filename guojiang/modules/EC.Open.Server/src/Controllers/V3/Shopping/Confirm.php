<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3\Shopping;

use Carbon\Carbon;
use Cart;
use DB;
use GuoJiangClub\Component\Address\RepositoryContract as AddressRepository;
use GuoJiangClub\Component\Discount\Applicators\DiscountApplicator;
use GuoJiangClub\Component\Discount\Models\Coupon;
use GuoJiangClub\Component\Discount\Models\Discount;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\EC\Open\Core\Applicators\PointApplicator;
use GuoJiangClub\EC\Open\Core\Processor\OrderProcessor;
use GuoJiangClub\EC\Open\Core\Services\DiscountService;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;

class Confirm extends Controller
{
    private $discountService;
    private $orderRepository;
    private $discountApplicator;
    private $addressRepository;
    private $pointRepository;
    private $pointApplicator;
    private $orderProcessor;

    public function __construct(
        DiscountService $discountService,
        OrderRepository $orderRepository,
        DiscountApplicator $discountApplicator,
        AddressRepository $addressRepository,
        PointRepository $pointRepository,
        OrderProcessor $orderProcessor,
        PointApplicator $pointApplicator
    ) {
        $this->discountService = $discountService;
        $this->orderRepository = $orderRepository;
        $this->discountApplicator = $discountApplicator;
        $this->addressRepository = $addressRepository;
        $this->pointRepository = $pointRepository;
        $this->pointApplicator = $pointApplicator;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke()
    {
        $user = request()->user();

        $order_no = request('order_no');
        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('submit', $order)) {
            return $this->failed('订单提交失败，无权操作');
        }

        if ($note = request('note')) {
            $order->note = $note;
        }

        //1. check stock.
        foreach ($order->getItems() as $item) { // 再次checker库存
            $model = $item->getModel();

            if (!$model->getIsInSale($item->quantity)) {
                return $this->failed('商品: '.$item->name.' '.$item->item_meta['specs_text'].' 库存不够，请重新下单');
            }
        }

        try {
            DB::beginTransaction();
            //2. apply the available discounts
            $discount = Discount::find(request('discount_id'));
            if (!empty($discount)) {
                if ($this->discountService->checkDiscount($order, $discount)) {
                    $order->type = Order::TYPE_DISCOUNT;

                    $this->discountApplicator->apply($order, $discount);
                } else {
                    return $this->failed('折扣信息有误，请确认后重试');
                }
            }
            //3. apply the available coupons
            if (empty($discount) or 1 != $discount->exclusive) {
                $coupon = Coupon::find(request('coupon_id'));
                if (!empty($coupon)) {
                    if (null != $coupon->used_at) {
                        return $this->failed('此优惠券已被使用');
                    }
                    if ($user->can('update', $coupon) and $this->discountService->checkCoupon($order, $coupon)) {
                        $this->discountApplicator->apply($order, $coupon);
                    } else {
                        return $this->failed('优惠券信息有误，请确认后重试');
                    }
                }
            }

            //4. use point
            if ($point = request('point') && config('ibrand.app.point.enable')) {
                if ($this->checkUserPoint($order, $point)) {
                    $this->pointApplicator->apply($order, $point);
                } else {
                    return $this->failed('积分不足或不满足积分折扣规则');
                }
            }

            //5. 保存收获地址信息。
            if (request('address_id') && $address = $this->addressRepository->find(request('address_id'))) {
                $order->accept_name = $address->accept_name;
                $order->mobile = $address->mobile;
                $order->address = $address->address;
                $order->address_name = $address->address_name;
            }

            //5. 保存订单状态
            $this->orderProcessor->submit($order);

            //6. remove goods store.
            foreach ($order->getItems() as $item) {
                $product = $item->getModel();
                $product->reduceStock($item->quantity);
                //$product->increaseSales($item->quantity);
                $product->save();
            }

            //8. 移除购物车中已下单的商品
            foreach ($order->getItems() as $orderItem) {
                if ($carItem = Cart::search(['name' => $orderItem->item_name])->first()) {
                    Cart::remove($carItem->rawId());
                }
            }

            //砍价
            if (Order::TYPE_REDUCE == $order->type) {
                event('order.reduce.special.type', [$order]);
                if (0 == $order->getNeedPayAmount()) {
                    $order->status = Order::STATUS_PAY;
                    $order->pay_time = Carbon::now();
                    $order->pay_status = 1;
                    $order->save();
                    event('order.paid', [$order]);
                }
            }

            DB::commit();

            return $this->success(['order' => $order], true);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage().$exception->getTraceAsString());

            return $this->failed('订单提交失败');
        }
    }

    private function checkUserPoint($order, $point)
    {
        $userPoint = $this->pointRepository->getSumPointValid($order->user_id);
        $usePointAmount = $point * config('ibrand.app.point.order_proportion');
        $orderPointLimit = $order->total * config('ibrand.app.point.order_limit');

        //如果用户的积分小于使用的积分 或者抵扣的金额大于了订单可抵扣金额，则无法使用该积分
        if ($userPoint < $point || $usePointAmount > $orderPointLimit) {
            return false;
        }

        return true;
    }
}
