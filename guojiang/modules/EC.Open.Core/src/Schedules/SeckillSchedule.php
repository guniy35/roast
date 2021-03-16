<?php

namespace GuoJiangClub\Component\Seckill;

use GuoJiangClub\Component\Seckill\Models\SeckillItem;
use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use iBrand\Scheduling\Scheduling;

class SeckillSchedule extends Scheduling
{
    public function schedule()
    {
        $this->schedule->call(function () {
            $orders = Order::where('status', Order::STATUS_NEW)->where('type', Order::TYPE_SECKILL)->get();
            if (count($orders) > 0) {
                foreach ($orders as $order) {
                    $seckill = $order->specialTypes()->where('origin_type', 'seckill_item')->first();
                    if ($seckill AND $seckillItem = SeckillItem::find($seckill->origin_id) AND $delayTime = $seckillItem->auto_close) {
                        $delayTime = Carbon::now()->addMinute(-$delayTime);
                        if ($order->submit_time < $delayTime) {
                            $order->status = Order::STATUS_CANCEL;
                            $order->completion_time = Carbon::now();
                            $order->cancel_reason = '秒杀过期未付款';
                            $order->save();
                            event('order.canceled', $order->id);
                            event('agent.order.canceled', $order->id);
                        }
                    }
                }
            }

        })->everyThirtyMinutes();
    }

}
