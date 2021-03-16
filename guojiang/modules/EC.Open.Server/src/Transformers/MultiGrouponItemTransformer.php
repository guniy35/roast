<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Transformers;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;

class MultiGrouponItemTransformer extends BaseTransformer
{
    protected $type;

    public function __construct($type = 'detail')
    {
        $this->type = $type;
    }

    public static $excludeable = [
        'deleted_at',
    ];

    public function transformData($model)
    {
        /*团长信息*/
        $leader = $model->users()->where('status', 1)->where('is_leader', 1)->first();
        $model->leader = $leader;

        $is_virtual = 0;
        $order = Order::where('id', $leader->order_id)->where('type', Order::TYPE_VIRTUAL_MULTI_GROUPON)->where('status', Order::STATUS_PAY)->first();
        if ($order) {
            $is_virtual = 1;
        }

        $model->is_virtual = $is_virtual;
        /*参团用户信息*/
        $model->users = $model->users()->where('status', 1)->get();

        /*是否已参与该子团*/
        $model->has_joined = 0;
        if ($user = auth('api')->user() and $grouponUser = $model->users()->where('user_id', $user->id)->first()) {
            $model->has_joined = 1;
            $model->multi_groupon_order_no = 1 == $grouponUser->order->pay_status ? '' : $grouponUser->order->order_no;

            if ('show' == $this->type) {
                $model->order_no = $grouponUser->order->order_no;
            }
        }

        /*差几人成团*/
        $model->gap_number = $model->getGapNumber();

        /*是否已过期*/
        $overdue_status = 0; //未过期
        if ($model->ends_at < Carbon::now()) {
            $overdue_status = 1;
        }
        $model->overdue_status = $overdue_status;

        $items = $model->toArray();

        return $items;
    }
}
