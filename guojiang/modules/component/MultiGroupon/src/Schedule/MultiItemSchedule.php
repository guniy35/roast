<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Schedule;

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponItems;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\Order\Models\Order;
use iBrand\Scheduling\Scheduling;

class MultiItemSchedule extends Scheduling
{
    public function schedule()
    {
        /*将未成团，到期的小团状态设置为过期状态*/
        $this->schedule->call(function () {
            $items = MultiGrouponItems::where('status', 0)->where('ends_at', '<', Carbon::now())->get();
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $item->status = 2;
                    $item->save();

                    $users = MultiGrouponUsers::where('multi_groupon_items_id', $item->id)->where('status', 1)->get();
                    if (count($users) > 0) {
                        foreach ($users as $user) {
                            $order = Order::find($user->order_id);
                            $order->status = Order::STATUS_CANCEL;
                            $order->completion_time = Carbon::now();
                            $order->cancel_reason = '团已过期';
                            $order->save();
                            event('order.canceled', $order->id);
                            event('agent.order.canceled', $order->id);
                        }
                    }
                }
            }
        })->everyMinute();
    }
}
