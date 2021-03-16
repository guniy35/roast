<?php


namespace GuoJiangClub\EC\Open\Server\Controllers\V3\Shopping;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Shipping\Models\Shipping;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;

class Delivery extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke()
    {
        $order_no = request('order_no');

        $order = $this->orderRepository->getOrderByNo($order_no);

        if (!$order_no || !$order || 2 != $order->status) {
            return $this->failed('订单不存在');
        }

        $data['delivery_time'] = date('Y-m-d H:i:s', Carbon::now()->timestamp);

        $data['order_id'] = $order->id;

        $data['method_id'] = mt_rand(1, 10);

        $data['tracking'] = uniqid();

        if (Shipping::create($data)) {
            $order->status = 3;
            $order->save();
            return $this->success();
        }

        return $this->failed('订单发货失败');
    }
}