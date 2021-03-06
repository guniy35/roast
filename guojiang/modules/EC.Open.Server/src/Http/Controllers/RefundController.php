<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/6
 * Time: 16:26
 */

namespace GuoJiangClub\EC\Open\Server\Http\Controllers;


use GuoJiangClub\Component\Order\Models\OrderItem;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Refund\Models\Refund;
use GuoJiangClub\Component\Refund\Models\RefundLog;
use GuoJiangClub\Component\Refund\Models\RefundShipping;
use GuoJiangClub\Component\Refund\Repositories\RefundRepository;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\EC\Open\Server\Transformers\OrderItemTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\RefundTransformer;
use Illuminate\Events\Dispatcher;

class RefundController extends Controller
{
    private $orderRepository;
    private $event;
    private $refundRepository;

    public function __construct(OrderRepository $orderRepository,
                                Dispatcher $event,
                                RefundRepository $refundRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->event = $event;
        $this->refundRepository = $refundRepository;
    }

    public function index()
    {
        $andConditions ['channel'] = 'ec';
        $andConditions ['user_id'] = request()->user()->id;
        if (request('status')) {
            $andConditions['status'] = request('status');
        }

        $limit = request('limit') ? request('limit') : 15;

        $orConditions = [];

        if ($criteria = request('criteria')) {
            $andConditions ['refund_no'] = ['refund_no', 'like', '%' . $criteria . '%'];
            $orConditions['order_no'] = ['order_no', 'like', '%' . $criteria . '%'];
            $orConditions['item_name'] = ['item_name', 'like', '%' . $criteria . '%'];
        }

        $lists = $this->refundRepository->getRefundsByCriteria($andConditions, $orConditions, $limit);

        return $this->success($lists);
    }

    public function show($refund_no)
    {
        if (!$refund = Refund::where('refund_no', $refund_no)->with('logs')->with('shipping')->with('orderItem')->get()->first()) {
            return $this->failed('????????????????????????');
        }

        $refund->logs->each(function ($item, $key) use ($refund) {
            $item->getRefundMsg($refund);
        });

        return $this->response()->item($refund, new RefundTransformer());
    }

    public function apply()
    {
        $user = request()->user();

        $order_no = request('order_no');
        if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
            return $this->response()->errorBadRequest('???????????????');
        }

        if (!$orderItem = OrderItem::find(request('order_item_id'))) {
            return $this->response()->errorForbidden('You need to pass into specific order item id');
        }

        if ($user->cant('refund', [$order, $orderItem])) {
            return $this->response()->errorForbidden('You have no right to refund this order.');
        }

        if (!request('quantity') OR $orderItem->quantity < request('quantity')) {
            return $this->response()->errorForbidden('??????????????????');
        }

        if ($grouponUser = MultiGrouponUsers::where('order_id', $order->id)->where('user_id', $user->id)->first() AND
            $grouponUser->status == 1 AND
            ($grouponUser->grouponItem->status == 0)
        ) {
            return $this->failed('???????????????????????????????????????');
        }

        //?????????????????????????????????????????????
        if (($orderItem->is_send == 0 AND $order->distribution_status != 1)
            AND request('type') == 4
        ) {
            return $this->response()->errorForbidden('????????????????????????');
        }

        //????????????????????????????????????????????????????????????????????????????????????????????????
        $refund = Refund::where('order_item_id', request('order_item_id'))->first();
        if ($refund AND $refund->type == 1 AND request('type') == 1 AND $orderItem->is_send == 1) {
            return $this->response()->errorForbidden('????????????????????????');
        }

        $input = array_filter(request()->only('order_item_id', 'images', 'type', 'quantity',
            'content', 'reason', 'amount'));

        if ($amount = intval(round(floatval(request('amount')) * 100))) {
            if ($orderItem->quantity <> $input['quantity']) {
                $theory = ($orderItem->total / $orderItem->quantity) * $input['quantity'];
                $temp = $amount;

                if ($theory < $temp) {
                    return $this->response()->errorForbidden('????????????????????????');
                }
            } else {
                if ($orderItem->total < $amount) {
                    return $this->response()->errorForbidden('????????????????????????');
                }
            }
            $input['amount'] = $amount;

        } else {
            $input['amount'] = ($orderItem->total / $orderItem->quantity) * $input['quantity'];
        }

        //1.???????????????????????????
        $input = array_merge(['user_id' => $user->id, 'order_id' => $order->id], $input);

        $refund = new Refund($input);
        $refund->save();

        $note = '';
        if ($input['type'] == 1) {
            $note = '????????????????????????';
        } elseif ($input['type'] == 4) {
            $note = '??????????????????????????????';
        }

        RefundLog::create(['refund_id' => $refund->id, 'user_id' => $user->id, 'action' => 'create', 'note' => $note]);

        $this->event->fire('order.refund.apply');

        return $this->success($refund);
    }

    public function returnRefund()
    {
        $refund_no = request('refund_no');
        if (!$refund_no || !$refund = Refund::where('user_id', request()->user()->id)->where('refund_no', $refund_no)->get()->first()) {
            return $this->response()->errorBadRequest('????????????????????????');
        }

        RefundLog::create(['refund_id' => $refund->id, 'user_id' => request()->user()->id
            , 'action' => 'express', 'note' => '???????????????', 'remark' => '???????????????' . request('shipping_name')
                . ', ?????????' . request('shipping_tracking')]);

        $refund->status = Refund::STATUS_USER_HAS_RETURNED;
        $refund->save();

        RefundShipping::create(['refund_id' => $refund->id,
            'code' => request('shipping_code'),
            'shipping_name' => request('shipping_name'),
            'shipping_tracking' => request('shipping_tracking')]);

        return $this->success($refund);

    }

    public function close()
    {
        $refund_no = request('refund_no');
        if (!$refund_no || !$refund = Refund::where('user_id', request()->user()->id)->where('refund_no', $refund_no)->get()->first()) {
            return $this->response()->errorBadRequest('????????????????????????');
        }

        if (($refund->type == 1 AND $refund->status != 0)
            OR
            ($refund->type == 4 AND $refund->status != 0 AND $refund->status != 5)
        ) {
            return $this->response()->errorBadRequest('???????????????');
        }

        RefundLog::create(['refund_id' => $refund->id, 'user_id' => request()->user()->id
            , 'action' => 'close', 'note' => '?????????????????????', 'remark' => request('remark')]);

        $refund->status = Refund::STATUS_CANCEL;
        $refund->save();

        return $this->success($refund);
    }

    public function baseInfo()
    {
        $item_id = request('order_item_id');
        if (!$item_id OR !$orderItem = OrderItem::find($item_id)) {
            return $this->response()->errorBadRequest('?????????????????????');
        }

        $type = [];
        $refund = $orderItem->refunds;
        if ($refund->count() == 1) {
            if ($orderItem->is_send == 1 OR
                ($orderItem->is_send == 0 AND $orderItem->order->distribution_status == 1)
            ) {
                //??????????????????????????????????????????????????????
                $type = [
                    ['key' => 4, 'value' => '????????????']
                ];
            } elseif ($orderItem->is_send == 0 AND $orderItem->order->distribution_status != 1) {
                $type = [
                    ['key' => 1, 'value' => '?????????']
                ];
            }
        } elseif ($refund->count() == 0) {
            if ($orderItem->is_send == 1 OR
                ($orderItem->order->distribution_status == 1 AND $orderItem->is_send == 0)
            ) {
                //?????????????????????2???????????????
                $type = [
                    ['key' => 1, 'value' => '?????????'],
                    ['key' => 4, 'value' => '????????????']
                ];
            } elseif ($orderItem->is_send == 0 AND $orderItem->order->distribution_status != 1) {
                //???????????????????????????????????????
                $type = [
                    ['key' => 1, 'value' => '?????????']
                ];
            }
        }

        return $this->response()->item($orderItem, new OrderItemTransformer())->setMeta(['type' => $type]);
    }

    /**
     * ????????????????????????
     * @return \Dingo\Api\Http\Response
     * ???index???????????????????????????
     */
    public function all()
    {
        $andConditions ['user_id'] = request()->user()->id;

        if (request('status')) {
            $andConditions['status'] = request('status');
        }

        $limit = request('limit') ? request('limit') : 15;

        $orConditions = [];

        if ($criteria = request('criteria')) {
            $andConditions ['refund_no'] = ['refund_no', 'like', '%' . $criteria . '%'];
            $orConditions['order_no'] = ['order_no', 'like', '%' . $criteria . '%'];
            $orConditions['item_name'] = ['item_name', 'like', '%' . $criteria . '%'];
        }

        $refunds = $this->refundRepository->getRefundsByCriteria($andConditions, $orConditions, $limit);

        return $this->response()->paginator($refunds, new RefundTransformer());

    }
}