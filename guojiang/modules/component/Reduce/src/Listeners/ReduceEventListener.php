<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Listeners;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use GuoJiangClub\Component\Reduce\Models\ReduceUsers;

class ReduceEventListener
{
    public function reduceOrderSpecialType(Order $order)
    {
        $reduceItems = $this->getReduceItems($order);

        if (!$reduceItems) {
            return;
        }

        $reduceItems->order_id = $order->id;

        $reduceItems->status = ReduceItems::STATUS_NEW;

        $reduceItems->order_time = Carbon::now()->toDateTimeString();

        $reduceItems->save();

        $collection = $reduceItems->with('users')->first();

        $collection->users->filter(function ($item) {
            $item->status = ReduceItems::STATUS_NEW;

            $item->save();
        });

        return $reduceItems;
    }

    public function reducePaid(Order $order)
    {
        $reduceItems = $this->getReduceItems($order);

        if (!$reduceItems) {
            return;
        }

        $reduceItems->complete_time = Carbon::now()->toDateTimeString();

        $nums = $reduceItems->reduce->store_nums;

        $reduceItems->reduce->store_nums = $nums;

        $reduceItems->save();

        $reduceItems->reduce->save();

        return $reduceItems;
    }

    public function checkStock(Order $order)
    {
        $reduceItems = $this->getReduceItems($order);

        if (!$reduceItems) {
            return;
        }

        if ($reduceItems->reduce->store_nums <= 0) {
            $order = Order::find($reduceItems->order_id);

            if (!$order) {
                return;
            }

            $order->status = Order::STATUS_CANCEL;

            $order->cancel_reason = '砍价过期未支付';

            $order->save();

            throw new \Exception('下手晚啦，该砍价活动已结束');
        }
    }

    public function cancelOrder($order_id)
    {
        $order = Order::find($order_id);

        if (Order::TYPE_REDUCE != $order->type) {
            return;
        }

        $special = $order->specialTypes()->first();

        $reduceItems = ReduceItems::find($special->origin_id);

        if (!$reduceItems) {
            return null;
        }

        $reduceItems->status = ReduceItems::STATUS_END;

        $reduceItems->order_id = null;

        $reduceItems->ends_at = Carbon::now()->toDateTimeString();

        $reduceItems->users->filter(function ($item) {
            $item->status = ReduceUsers::STATUS_NEW;

            $item->save();
        });

        $reduceItems->save();
    }

    protected function getReduceItems($order)
    {
        if (Order::TYPE_REDUCE != $order->type) {
            return;
        }

        $special = $order->specialTypes()->first();

        $reduceItems = ReduceItems::find($special->origin_id);

        if (!$reduceItems) {
            return null;
        }

        return $reduceItems;
    }

    public function subscribe($events)
    {
        $events->listen(
            'order.reduce.special.type',
            'GuoJiangClub\Component\Reduce\Listeners\ReduceEventListener@reduceOrderSpecialType'
        );

        $events->listen(
            'order.paid',
            'GuoJiangClub\Component\Reduce\Listeners\ReduceEventListener@reducePaid'
        );

        $events->listen(
            'order.canceled',
            'GuoJiangClub\Component\Reduce\Listeners\ReduceEventListener@cancelOrder'
        );

        $events->listen(
            'order.reduce.check.stock',
            'GuoJiangClub\Component\Reduce\Listeners\ReduceEventListener@checkStock'
        );
    }
}
