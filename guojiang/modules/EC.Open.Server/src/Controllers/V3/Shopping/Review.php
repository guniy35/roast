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
use GuoJiangClub\Component\Order\Models\Comment;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\OrderItem;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;
use DB;

class Review extends Controller
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
            $comments = request()->except('_token');

            if (!is_array($comments)) {
                return $this->failed('提交参数错误');
            }

            foreach ($comments as $key => $comment) {
                if (!isset($comment['order_no']) or !$order = $this->orderRepository->getOrderByNo($comment['order_no'])) {
                    return $this->failed('订单 ' . $comment['order_no'] . ' 不存在');
                }

                if (!isset($comment['order_item_id']) or !$orderItem = OrderItem::find($comment['order_item_id'])) {
                    return $this->failed('请选择具体评价的商品');
                }

                if ($user->cant('review', [$order, $orderItem])) {
                    return $this->failed('无权对该商品进行评价');
                }

                if ($order->comments()->where('order_item_id', $comment['order_item_id'])->count() > 0) {
                    return $this->failed('该产品已经评论，无法再次评论');
                }

                $content = isset($comment['contents']) ? $comment['contents'] : '';
                $point = isset($comment['point']) ? $comment['point'] : 5;
                $pic_list = isset($comment['images']) ? $comment['images'] : [];

                $comment = new Comment(['user_id' => $user->id, 'order_item_id' => $comment['order_item_id'], 'item_id' => $orderItem->item_id, 'item_meta' => $orderItem->item_meta
                    , 'contents' => $content, 'point' => $point, 'status' => 'show', 'pic_list' => $pic_list, 'goods_id' => $orderItem->item_meta['detail_id'],
                ]);

                $order->comments()->save($comment);

                $order->status = Order::STATUS_COMPLETE;
                $order->completion_time = Carbon::now();
                $order->save();
            }
            DB::commit();
            return $this->success();

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage() . $exception->getTraceAsString());
            return $this->failed($exception->getMessage());
        }
    }
}
