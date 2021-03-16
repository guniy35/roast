<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3\Shopping;

use Carbon\Carbon;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Address\Address;
use DB;

class ConfirmPoint extends Controller
{
	protected $orderRepository;
	protected $pointRepository;

	public function __construct(OrderRepository $orderRepository, PointRepository $pointRepository)
	{
		$this->orderRepository = $orderRepository;
		$this->pointRepository = $pointRepository;
	}

	public function __invoke()
	{
		$user     = request()->user();
		$order_no = request('order_no');
		if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
			return $this->failed('订单不存在');
		}

		if ($user->cant('submit', $order)) {
			return $this->failed('You have no right to submit this order');
		}

		if ($note = request('note')) {
			$order->note = $note;
		}

		foreach ($order->getItems() as $item) {
			$model = $item->type;
			$model = new $model();

			$product = $model->find($item->item_id);

			if (!$product->getIsInSale($item->quantity)) {
				return $this->failed('商品库存不够，请重新下单');
			}

			if (!$product->is_largess) {
				return $this->failed('非积分兑换商品');
			}
		}

		try {

			DB::beginTransaction();

			$userPoint = $this->pointRepository->getSumPointValid($user->id);

			if ($userPoint < $order->redeem_point) {
				return $this->failed('积分不够');
			}

			$this->pointRepository->create([
				'user_id'   => $order->user_id,
				'action'    => 'order_point',
				'note'      => '积分订单',
				'value'     => (-1) * $order->redeem_point,
				'item_type' => Order::class,
				'item_id'   => $order->id,
			]);

			event('point.change', $user->id);

			//5. 保存收获地址信息。
			if ($address = Address::find(request('address_id'))) {
				$order->accept_name  = $address->accept_name;
				$order->mobile       = $address->mobile;
				$order->address      = $address->address;
				$order->address_name = $address->address_name;
			}

			//5. 保存订单状态
			$order->status      = Order::STATUS_PAY;
			$order->submit_time = Carbon::now();
			$order->pay_time    = Carbon::now();
			$order->pay_status  = 1;
			$order->save();

			foreach ($order->getItems() as $item) {
				$model   = $item->type;
				$model   = new $model();
				$product = $model->find($item->item_id);
				$product->reduceStock($item->quantity);
				$product->increaseSales($item->quantity);
				$product->save();
			}

			DB::commit();

			return $this->success(['order' => $order]);
		} catch (\Exception $exception) {
			DB::rollBack();

			\Log::info($exception->getMessage() . $exception->getTraceAsString());

			return $this->failed($exception->getMessage());
		}
	}
}