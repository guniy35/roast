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
use GuoJiangClub\Component\Address\Repository as AddressRepository;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\OrderItem;
use GuoJiangClub\Component\Order\Models\SpecialType;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsLimitRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsUserLimitRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\MultiGrouponService;
use GuoJiangClub\EC\Open\Core\Services\SeckillService;
use GuoJiangClub\EC\Open\Core\Services\DiscountService;
use GuoJiangClub\Component\Reduce\Service\ReduceService;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;
use iBrand\Shoppingcart\Item;
use Illuminate\Support\Collection;

class Checkout extends Controller
{
	private $discountService;
	private $addressRepository;
	private $pointRepository;
	private $multiGrouponService;
	private $goodsLimit;
	private $goodsUserLimit;
	private $seckillService;
	private $reduceService;

	public function __construct(DiscountService $discountService,
	                            AddressRepository $addressRepository,
	                            PointRepository $pointRepository,
	                            MultiGrouponService $multiGrouponService,
	                            GoodsLimitRepository $goodsLimit,
	                            GoodsUserLimitRepository $goodsUserLimit,
	                            SeckillService $seckillService,
	                            ReduceService $reduceService
	)
	{
		$this->discountService     = $discountService;
		$this->addressRepository   = $addressRepository;
		$this->pointRepository     = $pointRepository;
		$this->multiGrouponService = $multiGrouponService;
		$this->goodsLimit          = $goodsLimit;
		$this->goodsUserLimit      = $goodsUserLimit;
		$this->seckillService      = $seckillService;
		$this->reduceService       = $reduceService;
	}

	public function __invoke()
	{
		$user = request()->user();

		$checkoutType = $this->getCheckoutType();

		$cartItems = call_user_func([$this, 'getSelectedItemFrom' . $checkoutType]);

		if (0 == $cartItems->count()) {
			return $this->failed('未选中商品，无法提交订单');
		}

		$order = new Order(['user_id' => request()->user()->id]);

		//小拼团
		if (!empty(request('multi_groupon_id'))) {
			$order->type = Order::TYPE_MULTI_GROUPON;
		}

		//砍价
		if (!empty(request('reduce_items_id'))) {
			$order->type = Order::TYPE_REDUCE;
		}

		//2. 生成临时订单对象
		$order = $this->buildOrderItemsFromCartItems($cartItems, $order);

		$defaultAddress = $this->addressRepository->getDefaultByUser(request()->user()->id);

		if (!$order->save()) {
			return $this->failed('订单提交失败，请重试');
		}

		//3.get available discounts
		list($discounts, $bestDiscountAdjustmentTotal, $bestDiscountId) = $this->getOrderDiscounts($order);

		//4. get available coupons
		list($coupons, $bestCouponID, $bestCouponAdjustmentTotal) = $this->getOrderCoupons($order, $user);

		//5. get point for order.
		$orderPoint = $this->getOrderPoint($order, $user);

		//6.生成运费
		$order->payable_freight = 0;

		//如果是小拼团,砍价订单不能使用促销活动和优惠券。
		if (Order::TYPE_MULTI_GROUPON == $order->type || Order::TYPE_REDUCE == $order->type) {
			$discounts  = [];
			$coupons    = [];
			$orderPoint = [];
		}

		if (Order::TYPE_MULTI_GROUPON == $order->type) {
			SpecialType::create(['order_id' => $order->id, 'origin_type' => 'multi_groupon', 'origin_id' => request('multi_groupon_id')]);
		}

		//砍价
		if (Order::TYPE_REDUCE == $order->type) {
			SpecialType::create(['order_id' => $order->id, 'origin_type' => 'reduce_items', 'origin_id' => request('reduce_items_id')]);
		}

		$discountGroup = $this->discountService->getOrderDiscountGroup($order, new Collection($discounts), new Collection($coupons));

		return $this->success([
			'order'                          => $order,
			'discounts'                      => $discounts,
			'coupons'                        => $coupons,
			'address'                        => $defaultAddress,
			'orderPoint'                     => $orderPoint,
			'best_discount_id'               => $bestDiscountId,
			'discountGroup'                  => $discountGroup,
			'best_coupon_id'                 => $bestCouponID,
			'best_coupon_adjustment_total'   => $bestCouponAdjustmentTotal,
			'best_discount_adjustment_total' => $bestDiscountAdjustmentTotal,
            'invoice_status' => settings('invoice_status'),
		]);
	}

	private function getCheckoutType()
	{
		if ($ids = request('cart_ids') and count($ids) > 0) {
			return 'Cart';
		}

		if (request('multi_groupon_id')) {
			return 'MultiGroupon';
		}

		if (request('reduce_items_id')) {
			return 'Reducen';
		}

		if (request('product_id')) {
			return 'Product';
		}

		if (request('seckill_item_id')) {
			return 'Seckill';
		}

		return 'Cart';
	}

	/**
	 * @param $cartItems
	 * @param $order
	 *
	 * @return OrderItem
	 */
	private function buildOrderItemsFromCartItems($cartItems, $order)
	{
		foreach ($cartItems as $key => $item) {
			if (0 == $item->qty) {
				continue;
			}

			$item_meta = [
				'image'      => $item->img,
				'detail_id'  => $item->model->detail_id,
				'specs_text' => $item->model->specs_text,
			];

			$orderItem = new OrderItem(['quantity' => $item->qty, 'unit_price' => $item->model->sell_price,
			                            'item_id'  => $item->id, 'type' => $item->__model, 'item_name' => $item->name, 'item_meta' => $item_meta,
			]);

			if (Order::TYPE_MULTI_GROUPON == $order->type || Order::TYPE_REDUCE) { //拼团砍价设置为商品价格
				$orderItem->unit_price  = $item->price;
				$orderItem->units_total = $item->price * $item->qty;
			}

			$orderItem->recalculateUnitsTotal();

			$order->addItem($orderItem);
		}

		return $order;
	}

	/**
	 * get order discounts data.
	 *
	 * @param $order
	 *
	 * @return array
	 */
	private function getOrderDiscounts($order)
	{
		$bestDiscountAdjustmentTotal = 0;
		$bestDiscountId              = 0;

		$discounts = $this->discountService->getEligibilityDiscounts($order);

		if ($discounts) {
			if (0 == count($discounts)) { //修复过滤后discount为0时非false 的问题。
				$discounts = [];
			} else {
				$bestDiscount                = $discounts->sortBy('adjustmentTotal')->first();
				$bestDiscountId              = $bestDiscount->id;
				$bestDiscountAdjustmentTotal = -$bestDiscount->adjustmentTotal;
				$discounts                   = collect_to_array($discounts);
			}
		} else {
			$discounts = [];
		}

		return [$discounts, $bestDiscountAdjustmentTotal, $bestDiscountId];
	}

	/**
	 * @param $order
	 * @param $user
	 *
	 * @return array|bool
	 */
	private function getOrderCoupons($order, $user)
	{
		$bestCouponID              = 0;
		$bestCouponAdjustmentTotal = 0;
		$cheap_price               = 0;

		$coupons = $this->discountService->getEligibilityCoupons($order, $user->id);

		if ($coupons) {
			$bestCoupon = $coupons->sortBy('adjustmentTotal')->first();
			if ($bestCoupon->orderAmountLimit > 0 and $bestCoupon->orderAmountLimit > ($order->total + $cheap_price)) {
				$bestCouponID = 0;
			} else {
				$bestCouponID              = $bestCoupon->id;
				$cheap_price               += $bestCoupon->adjustmentTotal;
				$bestCouponAdjustmentTotal = -$bestCoupon->adjustmentTotal;
			}

			$coupons = collect_to_array($coupons);
		} else {
			$coupons = [];
		}

		return [$coupons, $bestCouponID, $bestCouponAdjustmentTotal];
	}

	private function getOrderPoint($order, $user)
	{
		if (config('ibrand.app.point.enable')) {
			$orderPoint['userPoint']    = $this->pointRepository->getSumPointValid($user->id); //用户可用积分
			$orderPoint['pointToMoney'] = config('ibrand.app.point.order_proportion');  //pointToMoney
			$orderPoint['pointLimit']   = config('ibrand.app.point.order_limit') / 100; //pointLimit
			$pointAmount                = min($orderPoint['userPoint'] * $orderPoint['pointToMoney'], $order->total * $orderPoint['pointLimit']);
			$orderPoint['pointAmount']  = -$pointAmount;
			$orderPoint['pointCanUse']  = $pointAmount / $orderPoint['pointToMoney'];
		}

		return [];
	}

	/**
	 * call by call_user_func().
	 *
	 * @return bool|Collection
	 *
	 * @throws \Exception
	 */
	private function getSelectedItemFromCart()
	{
		//获取购物车中选中的商品数据

		$ids = request('cart_ids');

		if (!$ids || 0 == count($ids)) {
			return false;
		}

		$cartItems = new Collection();

		foreach ($ids as $cartId) {
			$cartItems->put($cartId, Cart::get($cartId));
		}

		foreach ($cartItems as $key => $item) {
			//检查库存是否足够
			if (!$this->checkItemStock($item)) {
				Cart::update($key, ['message' => '库存数量不足', 'status' => 'onhand']);

				throw new \Exception('商品: ' . $item->name . ' ' . $item->color . ',' . $item->size . ' 库存数量不足');
			}

			$this->checkItemLimit($item);
		}

		return $cartItems;
	}

	/**
	 * call by call_user_func().
	 *
	 * @return Collection
	 *
	 * @throws \Exception
	 */
	private function getSelectedItemFromSeckill()
	{
		$user = request()->user();

		$cartItems = new Collection();
		if (!empty(request('seckill_item_id'))) {
			$buys = request()->all();

			$this->seckillService->checkOrderSeckillInfo($buys, $user->id);

			$cartItems = $this->seckillService->makeCartItems($buys);
			//拼团活动
		}

		foreach ($cartItems as $key => $item) {
			//检查库存是否足够
			if (!$this->checkItemStock($item)) {
				throw new \Exception('商品: ' . $item->name . ' ' . $item->color . ',' . $item->size . ' 库存数量不足');
			}
		}

		return $cartItems;
	}

	/**
	 * 小拼团数据检测.
	 *
	 * @return Collection
	 *
	 * @throws \Exception
	 */
	private function getSelectedItemFromMultiGroupon()
	{
		$user      = request()->user();
		$cartItems = new Collection();
		if ($multiGrouponID = request('multi_groupon_id')) {
			\Log::info($multiGrouponID);
			$buys = request()->all();

			$this->multiGrouponService->checkGrouponStatusByUser($user->id, $multiGrouponID, request('multi_groupon_item_id'));

			$cartItems = $this->multiGrouponService->makeCartItems($buys, $multiGrouponID);
		}

		foreach ($cartItems as $key => $item) {
			//检查库存是否足够
			if (!$this->checkItemStock($item)) {
				throw new \Exception('商品: ' . $item->name . ' ' . $item->color . ',' . $item->size . ' 库存数量不足');
			}
		}

		return $cartItems;
	}

	private function getSelectedItemFromProduct()
	{
		$cartItems = new Collection();

		$productId = request('product_id');

		$__raw_id = md5(time() . request('product_id'));

		$product = Product::find($productId);

		$item = request()->all();

		$input = ['__raw_id' => $__raw_id,
		          'id'       => $productId,    //如果是SKU，表示SKU id，否则是SPU ID
		          //'name'     => $product->name,
		          'img'      => isset($item['attributes']['img']) ? $item['attributes']['img'] : '',
		          'qty'      => request('qty'), //团购商品数据恒为1
		          //'price'    => $product->sell_price,
		          'total'    => isset($item['total']) ? $item['total'] : '',
		];

		if (isset($item['attributes']['sku'])) {
			$input['name']  = $product->name;
			$input['price'] = $product->sell_price;

			$input['color']   = isset($item['attributes']['color']) ? $item['attributes']['color'] : [];
			$input['size']    = isset($item['attributes']['size']) ? $item['attributes']['size'] : [];
			$input['com_id']  = isset($item['attributes']['com_id']) ? $item['attributes']['com_id'] : [];
			$input['type']    = 'sku';
			$input['__model'] = Product::class;
		} else {
			$goods          = Goods::find($productId);
			$input['name']  = $goods->name;
			$input['price'] = $goods->sell_price;

			$input['size']    = isset($item['size']) ? $item['size'] : '';
			$input['color']   = isset($item['color']) ? $item['color'] : '';
			$input['type']    = 'spu';
			$input['__model'] = Goods::class;
			$input['com_id']  = $item['id'];
		}

		$data = new Item(array_merge($input), $item);

		$cartItems->put($__raw_id, $data);

		return $cartItems;
	}

	private function checkItemStock($item)
	{
		if (is_null($item->model) || !$item->model->getIsInSale($item->qty)) {
			return false;
		}

		return true;
	}

	/**
	 * @param $item
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function checkItemLimit($item)
	{

		$goods_limit = $this->goodsLimit->findWhere(['goods_id' => $item->com_id, 'activity' => 1, ['starts_at', '<=', Carbon::now()], ['ends_at', '>=', Carbon::now()]])->first();
		if (!$goods_limit) {
			return true;
		}

		$buy_num = $goods_limit->quantity;
		$check   = $this->goodsUserLimit->findWhere(['user_id' => request()->user()->id, 'goods_id' => $item->com_id])->first();
		if ($check) {
			$buy_num = $goods_limit->quantity - $check->buy_nums;
		}

		$start_at = strtotime($goods_limit->starts_at);
		$end_at   = strtotime($goods_limit->ends_at);
		if (time() >= $start_at && time() <= $end_at && $item->qty > $buy_num) {
			throw new \Exception('商品: ' . $item->name . '每人限购 ' . $goods_limit->quantity . '件');
		}

		return true;
	}

	/**
	 * 砍价数据检测.
	 *
	 * @return Collection
	 *
	 * @throws \Exception
	 */
	private function getSelectedItemFromReducen()
	{
		$user      = request()->user();
		$cartItems = new Collection();
		if ($reduce_items_id = request('reduce_items_id')) {
			\Log::info($reduce_items_id);
			$buys = request()->all();

			$this->reduceService->checkReduceStatusByUser($user->id, $reduce_items_id);

			$cartItems = $this->reduceService->makeCartItems($buys, $reduce_items_id);
		}

		foreach ($cartItems as $key => $item) {
			//检查库存是否足够
			if (!$this->checkItemStock($item)) {
				throw new \Exception('商品: ' . $item->name . ' ' . $item->color . ',' . $item->size . ' 库存数量不足');
			}
		}

		return $cartItems;
	}
}
