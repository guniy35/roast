<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3\Shopping;

use GuoJiangClub\Component\Address\Repository as AddressRepository;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\EC\Open\Core\Processor\OrderProcessor;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\OrderItem;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product;

class CheckoutPoint extends Controller
{
	protected $pointRepository;
	protected $addressRepository;
	protected $orderProcessor;

	public function __construct(PointRepository $pointRepository, AddressRepository $addressRepository, OrderProcessor $orderProcessor)
	{
		$this->pointRepository   = $pointRepository;
		$this->addressRepository = $addressRepository;
		$this->orderProcessor    = $orderProcessor;
	}

	public function __invoke()
	{
		try {

			$user = request()->user();
			if (!$user->mobile) {
				return $this->failed('您没有绑定手机');
			}

			if (empty(request('goods_id')) AND empty(request('product_id'))) {
				return $this->failed('请求错误，请选择商品');
			}

			$order = new Order(['user_id' => $user->id, 'type' => Order::TYPE_POINT, 'channel' => 'integral']);

			if (request('goods_id')) {
				$model = Goods::find(request('goods_id'));
			} else {
				$model = Product::find(request('product_id'));
			}

			if (!$model) {
				return $this->failed('商品不存在');
			}

			$quantity = request('quantity') ? request('quantity') : 1;
			if (!$model->getIsInSale($quantity)) {
				return $this->failed('库存不够');
			}

			if (!$model->is_largess) {
				return $this->failed('非积分兑换商品');
			}

			$userPoint = $this->pointRepository->getSumPointValid($user->id);
			if ($model->redeem_point > $userPoint) {
				return $this->failed('积分不够，无法兑换此商品');
			}

			$order->redeem_point = $model->redeem_point * $quantity;

			$item_meta = [
				'image'     => $model->photo_url,
				'detail_id' => $model->detail_id,
			];

			$item_meta['specs_text'] = $model->specs_text;

			$orderItem = new OrderItem([
				'quantity'   => $quantity,
				'unit_price' => $model->sell_price,
				'item_id'    => $model->id,
				'type'       => get_class($model),
				'item_name'  => $model->name,
				'item_meta'  => $item_meta,
			]);

			$orderItem->recalculateUnitsTotal();

			$order->addItem($orderItem);

			$defaultAddress = $this->addressRepository->getDefaultByUser($user->id);
			if (!$order = $this->orderProcessor->create($order)) {
				return $this->failed('订单提交失败，请确认后重试');
			}

			$discounts = [];
			$coupons   = [];

			return $this->success([
				'order'          => $order,
				'discounts'      => $discounts,
				'coupons'        => $coupons,
				'address'        => $defaultAddress,
				'invoice_status' => settings('invoice_status'),
			], true);
		} catch (\Exception $exception) {
			return $this->failed($exception->getMessage());
		}
	}
}