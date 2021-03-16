<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Schedule;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Reduce\Models\Reduce;
use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use iBrand\Scheduling\Scheduling;

class ReduceSchedule extends Scheduling
{
    public function schedule()
    {
        /*库存为O的砍价活动item过期的取消订单*/
        $this->schedule->call(function () {
            $items = ReduceItems::whereNull('complete_time')->whereNotNull('order_id')->get();

            $items->filter(function ($item) {
                $nums = $item->reduce->store_nums;

                if ($item->ends_at <= Carbon::now() || $nums <= 0) {
                    $item->status = ReduceItems::STATUS_END;

                    $item->save();
                }

                if ($nums <= 0) {
                    $item->reduce->status = Reduce::STATUS_END;

                    $item->reduce->save();
                }

                $order = Order::find($item->order_id);

                if (!$order) {
                    return;
                }

                $order->status = Order::STATUS_CANCEL;

                $order->cancel_reason = '砍价过期未支付';

                $order->save();
            });
        })->everyMinute();
    }
}
