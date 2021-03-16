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
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;
use DB;

class Received extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke()
    {
        try {

            DB::beginTransaction();

            $user = request()->user();

            $order_no = request('order_no');
            if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
                return $this->failed('订单不存在');
            }

            if ($user->cant('received', $order)) {
                return $this->failed('无法对此订单进行确认收货操作');
            }

            $order->status = Order::STATUS_RECEIVED;
            $order->accept_time = Carbon::now();
            $order->save();

            DB::commit();

            return $this->success();

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage().$exception->getTraceAsString());
            return $this->failed($exception->getMessage());
        }
    }
}
