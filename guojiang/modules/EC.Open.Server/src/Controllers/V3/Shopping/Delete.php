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

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;

class Delete extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke()
    {
        $user = request()->user();

        $order_no = request('order_no');
        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('delete', $order)) {
            return $this->failed('无权删除此订单');
        }

        $order->status = Order::STATUS_DELETED;
        $order->save();

        return $this->success();
    }
}
