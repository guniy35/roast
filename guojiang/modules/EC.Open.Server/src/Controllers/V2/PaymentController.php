<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V2;

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Payment\Services\PaymentService;
use iBrand\Component\Pay\Facades\Charge;
use iBrand\Component\Pay\Facades\PayNotify;

class PaymentController extends Controller
{
    private $payment;
    private $orderRepository;

    public function __construct(PaymentService $paymentService,
                                OrderRepository $orderRepository
    ) {
        $this->payment = $paymentService;
        $this->orderRepository = $orderRepository;
    }

    public function paidSuccess()
    {
        $user = request()->user();
        $order_no = request('order_no');

        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->failed('订单不存在');
        }

        if ($user->cant('update', $order)) {
            return $this->failed('无权操作.');
        }

        //在pay_debug=true 状态下，可以调用此接口直接更改订单支付状态
        if (config('ibrand.app.pay_debug') and request('charge_id')) {
            $charge = \iBrand\Component\Pay\Models\Charge::where('order_no', $order_no)->orderBy('created_at', 'desc')->first();
            $charge->transaction_no = '';
            $charge->time_paid = Carbon::now();
            $charge->paid = 1;
            $charge->channel = 'test';
            $charge->amount = $order->total;
            $charge->save();
            $order = PayNotify::success($charge->type, $charge);
        }

        if (!config('ibrand.app.pay_debug') and $order_no->total > 0) {
            //同步查询微信订单状态，防止异步通信失败导致订单状态更新失败

            $charge = Charge::find(request('charge_id'));

            $order = PayNotify::success($charge->type, $charge);
        }

        $multiGroupon = '';
        if (Order::TYPE_MULTI_GROUPON == $order->type) {
            $multiGroupon = MultiGrouponUsers::where('order_id', $order->id)->first();
        }
        $order->multiGroupon = $multiGroupon;

        if (Order::STATUS_PAY == $order->status) {
            return $this->success(['order' => $order, 'payment' => '微信支付']);
        }

        return $this->failed('支付失败');
    }
}
