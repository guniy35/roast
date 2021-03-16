<?php

namespace GuoJiangClub\Component\Refund;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Refund\Models\Refund;
use GuoJiangClub\Component\Refund\Service\RefundService;
use GuoJiangClub\Scheduling\Scheduling;

class Schedule extends Scheduling
{
    public function schedule()
    {
        /* 5天商家未处理，自动同意售后*/
        $this->schedule->call(function () {
            \Log::info('进入了售后自动处理');
            $delay   = app('system_setting')->getSetting('refund_auto_processing') ? app('system_setting')->getSetting('refund_auto_processing') : 5;
            $refunds = Refund::where('status', 0)->whereRaw('(DATEDIFF(now(),created_at) >= ' . $delay . ')')->get();
            if (count($refunds) > 0) {
                $service = new RefundService();
                foreach ($refunds as $item) {
                    $order = Order::find($item->order_id);
                    $service->refundLog($item->id, 0, 9999, 'agree', '系统自动同意申请', '');

                    /*if ($order->distribution_status == 0) {*/
                    if ($item->type == 1) {
                        /*$service->refundLog($item->id, 0, 9999, 'receipt', '本次申请已完成', '');*/

                        $item->status = Refund::STATUS_SHOP_PAID;
                        $item->save();

                        $refundModel = Refund::find($item->id);

                        /*if ($service->checkOrderRefund($order, $item) OR env('JW_REFUND')) {*/
                        if ($service->checkOrderRefund($order, $item)) {
                            $order->status = 6;
                            $order->save();

                            /*取消积分以及分销佣金*/
                            event('order.canceled', $order->id);
                            event('agent.order.canceled', $order->id);
                        } else {
                            event('order.refund.complete', $refundModel);
                            event('agent.order.refund', $refundModel);
                        }
                        $this->changeOrderStatus($order->id);
                    } else {
                        $item->status = 5;
                        $item->save();
                    }
                    $refund = \GuoJiangClub\Store\Backend\Model\Refund::find($item->id);
                    event('refund.agree', [$refund, $order->id]);
                }
            }
        })->dailyAt('1:15');
    }

    /**
     * 如果部分发货，所有售后已完成，更改订单状态为已发货
     *
     * @param $order_id
     */
    private function changeOrderStatus($order_id)
    {
        $order = \GuoJiangClub\Store\Backend\Model\Order::find($order_id);
        if ($order->status != 2) {
            return;
        }
        $sendItem    = $order->items()->where('is_send', 1)->get();
        $sendItemNum = count($sendItem);

        $unSendItem    = $order->items()->where('is_send', 0)->get();
        $unSendItemNum = $unSendItem->sum('quantity');

        $filtered    = $order->refunds->filter(function ($item) {  //未发货的item已完成的售后
            return $item->orderItem->is_send == 0 and ($item->status == 8 or $item->status == 3);
        });
        $filteredNum = $filtered->sum('quantity');

        if ($sendItemNum != 0 and $unSendItemNum != 0 and $unSendItemNum == $filteredNum) {
            $shipping                   = $order->shipping->last();
            $order->distribution_status = 1;
            $order->status              = 3;
            $order->send_time           = $shipping->delivery_time;
            $order->distribution        = $shipping->id;
            $order->save();
        }
    }
}
