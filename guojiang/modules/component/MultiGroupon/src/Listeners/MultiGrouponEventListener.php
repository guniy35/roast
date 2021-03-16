<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Listeners;

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponItems;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\User\Models\User;

class MultiGrouponEventListener
{
    /*
     * 订单付款创建小团信息
     */
    public function onJoinGroupon(Order $order)
    {
        if (Order::TYPE_MULTI_GROUPON != $order->type && Order::TYPE_VIRTUAL_MULTI_GROUPON != $order->type) {
            return;
        }

        $special = $order->specialTypes()->first();
        $grouponID = $special->origin_id;

        $grouponUser = MultiGrouponUsers::where([
            'multi_groupon_id' => $grouponID,
            'status' => 0,
            'user_id' => $order->user_id,
            'order_id' => $order->id, ])->first();

        if (!$grouponUser) {
            return;
        }

        /*创建小团信息*/
        if (0 == $grouponUser->multi_groupon_items_id) {
            $item = MultiGrouponItems::create([
                'multi_groupon_id' => $grouponID,
                'multi_groupon_goods_id' => $grouponUser->belongsToGroupon->goods_id,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addHours(24),
            ]);

            /*更新user状态*/
            $grouponUser->multi_groupon_items_id = $item->id;
            $grouponUser->is_leader = 1;
            $grouponUser->status = 1;
        } else {
            $grouponUser->status = 1;
        }
        $grouponUser->save();

        /*如果人数已满，更新团的状态*/
        $grouponItem = MultiGrouponItems::find($grouponUser->multi_groupon_items_id);
        if ($grouponItem->getTotalUser() == $grouponItem->groupon->number) {
            $grouponItem->status = 1;
            $grouponItem->complete_time = Carbon::now();
            $grouponItem->save();

            $grouponUser = $grouponItem->users();
            foreach ($grouponUser as $item) {
                if (1 == $item->status) {
                    $user = User::find($item->user_id);
                    //$user->notify(new GrouponSuccess(['grouponUser' => $item, 'order' => $order]));
                }
            }
        }
    }

    /**
     * 下单创建用户团信息.
     *
     * @param Order $order
     */
    public function onCreateGrouponOrder(Order $order)
    {
        if (Order::TYPE_MULTI_GROUPON != $order->type && Order::TYPE_VIRTUAL_MULTI_GROUPON != $order->type) {
            return;
        }

        $user = request()->user();
        $meta['avatar'] = $user->avatar;
        $meta['nick_name'] = $user->nick_name;

        MultiGrouponUsers::create([
            'user_id' => $order->user_id,
            'multi_groupon_id' => request('multi_groupon_id'),
            'multi_groupon_items_id' => request('multi_groupon_item_id') ? request('multi_groupon_item_id') : 0,
            'meta' => $meta,
            'order_id' => $order->id,
        ]);
    }

    public function onCancelOrder($order_id)
    {
        $order = Order::find($order_id);
        if (Order::TYPE_MULTI_GROUPON != $order->type) {
            return;
        }

        foreach ($order->getItems() as $item) {
            $model = $item->type;
            $model = new $model();
            $product = $model->find($item->item_id);
            $product->restoreStock($item->quantity);
            $product->restoreSales($item->quantity);
            $product->save();
        }

        /*if ($order->refunds()->count() > 0) {
            return;
        }*/

        if ($multiUser = MultiGrouponUsers::where('order_id', $order_id)->first()) {
            $user = User::find($order->user_id);
            //$user->notify(new GrouponFailed(['grouponUser' => $multiUser, 'order' => $order]));

            if (0 == $multiUser->status) {
                $multiUser->delete();
            }
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'order.paid',
            'GuoJiangClub\Component\MultiGroupon\Listeners\MultiGrouponEventListener@onJoinGroupon'
        );

        $events->listen(
            'order.submitted',
            'GuoJiangClub\Component\MultiGroupon\Listeners\MultiGrouponEventListener@onCreateGrouponOrder'
        );

        $events->listen(
            'order.canceled',
            'GuoJiangClub\Component\MultiGroupon\Listeners\MultiGrouponEventListener@onCancelOrder'
        );
    }
}
