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

use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Core\Processor\OrderProcessor;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;

class Cancel extends Controller
{
    private $orderRepository;
    private $orderProcessor;

    public function __construct(OrderRepository $orderRepository, OrderProcessor $orderProcessor)
    {
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke()
    {
        $user = request()->user();

        $order_no = request('order_no');
        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('cancel', $order)) {
            return $this->failed('无法取消该订单');
        }

        $this->orderProcessor->cancel($order);

        //TODO: 用户未付款前取消订单后，需要还原库存
        foreach ($order->getItems() as $item) {
            $product = $item->getModel();
            $product->restoreStock($item->quantity);
            $product->restoreSales($item->quantity);
            $product->save();
        }

        return $this->success();
    }
}
