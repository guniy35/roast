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

use GuoJiangClub\Component\Discount\Repositories\DiscountRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRepository;
use GuoJiangClub\EC\Open\Core\Models\Goods;
use GuoJiangClub\EC\Open\Core\Services\DiscountService;
use GuoJiangClub\EC\Open\Server\Transformers\DiscountTransformer;

class DiscountController extends Controller
{
    private $discount;
    protected $agentRepository;

    public function __construct(DiscountRepository $discountRepository, AgentRepository $agentRepository)
    {
        $this->discount = $discountRepository;
        $this->agentRepository = $agentRepository;
    }

    public function create()
    {
        $discount = $this->discount->create(request()->except('rule_type', 'rule_value', 'action_type', 'action_value'));

        $ruleType = request('rule_type');

        if ('cart_quantity' == $ruleType) {
            $ruleData = ['count' => request('rule_value')];
        } else {
            $ruleData = ['amount' => request('rule_value')];
        }

        $actionType = request('action_type');

        if ('order_fixed_discount' == $actionType) {
            $actionData = ['amount' => request('action_value')];
        } else {
            $actionData = ['percentage' => request('action_value')];
        }

        $discount->rules()->create(['type' => $ruleType, 'configuration' => json_encode($ruleData)]);
        $discount->actions()->create(['type' => $actionType, 'configuration' => json_encode($actionData)]);

        return $this->success();
    }

    public function shoppingCartDiscount()
    {
        $ids = request('ids');

        if (empty($ids)) {
            return $this->failed('必填参数缺失');
        }

        $discount = [];
        $coupon = [];
        foreach ($ids as $id) {
            $goods = Goods::find($id);
            if (!$goods) {
                continue;
            }

            $discounts = app(DiscountService::class)->getDiscountsByGoods($goods);

            if (!$discounts || 0 == count($discounts)) {
                continue;
            }

            $coupon_based_0 = collect_to_array($discounts->where('coupon_based', 0));
            foreach ($coupon_based_0 as $d) {
                if (!array_key_exists($d['id'], $discount)) {
                    $discount[$d['id']] = $d;
                }
            }

            $coupon_based_1 = collect_to_array($discounts->where('coupon_based', 1));
            foreach ($coupon_based_1 as $c) {
                if (!array_key_exists($c['id'], $coupon)) {
                    $coupon[$c['id']] = $c;
                }
            }
        }

        $result = ['coupons' => array_values($coupon), 'discounts' => array_values($discount)];

        return $this->success($result);
    }

    /**
     * 获取优惠券、促销活动列表.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getDiscountList()
    {
        $channel = 1 == request('type') ? 'shop' : 'ec';

        $is_agent_share = $this->is_agent();  //分销员可分享的优惠券列表
        $coupons = $this->discount->getDiscountByType(request('is_coupon'), $channel, $limit = 10, $is_agent_share);

        foreach ($coupons as $key => $coupon) {
            if ($coupon->rules()->where('type', 'contains_wechat_group')->first()) {
                unset($coupons[$key]);
            }
        }

        return $this->response()->paginator($coupons, new DiscountTransformer('list'));
    }

    public function is_agent()
    {
        if ($user = auth('api')->user() and \request('is_agent')) {
            return $this->agentRepository->findWhere(['status' => 1, 'user_id' => $user->id])->first();
        }

        return null;
    }

    /**
     * 促销活动 优惠券通用接口.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getDiscountDetailByID($id)
    {
        $discount = $this->discount->find($id);

        /*if ($discount->coupon_based == 1 AND $discount->is_agent_share == 1 AND request('is_agent')) {
            $agent = $this->is_agent();
            if ($agent) {
                $discount->agent_code = $agent->code;
            }
        }*/

        return $this->response()->item($discount, new DiscountTransformer('detail'));
    }
}
