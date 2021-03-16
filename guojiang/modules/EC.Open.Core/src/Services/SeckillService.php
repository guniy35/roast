<?php

namespace GuoJiangClub\EC\Open\Core\Services;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Seckill\Repositories\SeckillItemRepository;
use GuoJiangClub\Component\Product\Models\Product;
use Illuminate\Support\Collection;
use iBrand\Shoppingcart\Item;
use GuoJiangClub\Component\Seckill\Models\SeckillItem;

class SeckillService
{
    private $seckillItemRepository;

    public function __construct(SeckillItemRepository $seckillItemRepository)
    {
        $this->seckillItemRepository = $seckillItemRepository;
    }


    public function checkOrderSeckillInfo($buys, $user_id)
    {
        if (!$buys || !isset($buys['id'])) throw new \Exception('秒杀商品数据不存在');

        if (isset($buys['attributes']['dynamic_sku'])) {
            $goods = Product::find($buys['id']);
            $goods_id = $goods->goods_id;
        } else {
            $goods = Goods::find($buys['id']);
            $goods_id = $goods->id;
        }


        if (!$buys['seckill_goods_id'] ||
            !isset($buys['qty']) ||
            !$buys['total'] ||
            !$goods ||
            $goods_id != $buys['seckill_goods_id'] ||
            !isset($buys['price']) ||
            !$seckill_item = $this->seckillItemRepository->CheckSeckillItemInfo($buys['seckill_item_id'], $buys['price'], $buys['seckill_goods_id'])
        ) {
            throw new \Exception('秒杀商品不存在');
        }

        //秒杀判断限购
        if ($seckill_item And $seckill_item->init_status == SeckillItem::ING And $seckill_item->limit) {
            $count = $this->seckillItemRepository->getUserSeckillGoodsCountByItemId($seckill_item->id, $user_id);
            $limit = $count > $seckill_item->limit ? 0 : $seckill_item->limit - $count;
            if (!$limit) {
                $str = '商品:' . $buys['name'] . '每人限购' . $seckill_item->limit . '件';
                throw new \Exception($str);
            }
        }
        if ($seckill_item->limit != 0) {
            if ($seckill_item->limit < $buys['qty']) {
                $str = '商品:' . $buys['name'] . '每人限购' . $seckill_item->limit . '件';
                throw new \Exception($str);
            }
        }
        if (number_format($buys['total'], 2, ".", "") !== number_format($seckill_item->seckill_price * $buys['qty'], 2, ".", "")) {
            throw new \Exception('秒杀商品价格信息有误');
        }

    }


    public function checkSeckillMaxOnlineUser()
    {

        $seckill_max_online_user = settings('seckill_max_online_user') ? settings('seckill_max_online_user') : 0;

        //未开启直接返回
        if (!$seckill_max_online_user) {
            return true;
        }

        $onlineUserCount = count(cache('online_user_count'));

        //在线人数小于最大人数，直接返回true
        if ($onlineUserCount < $seckill_max_online_user) {
            return true;
        }

        //超过最大人数以后，百分比人数可以提交秒杀订单
        $seckill_max_online_user_pass_rate = settings('seckill_max_online_user_pass_rate') ? settings('seckill_max_online_user_pass_rate') : 1;

        $rand = mt_rand(1, 100);

        if ($rand < $seckill_max_online_user_pass_rate) {
            return true;
        }

        return false;
    }


    public function makeCartItems($buys)
    {

        $cartItems = new Collection();

        $buys_new[] = $buys;

        foreach ($buys_new as $k => $item) {

            $__raw_id = md5(time() . $k);

            $input = ['__raw_id' => $__raw_id,
                'com_id' => isset($item['id']) ? $item['id'] : '',  //如果是有sku，表示SKU id，否则是goods_id
                'name' => isset($item['name']) ? $item['name'] : '',
                'img' => isset($item['img']) ? $item['img'] : '',
                'qty' => isset($item['qty']) ? $item['qty'] : '',
                'price' => isset($item['price']) ? $item['price'] : '',
                'total' => isset($item['total']) ? $item['total'] : '',
            ];

            if (isset($item['attributes']['dynamic_sku'])) {
                $input['color'] = isset($item['attributes']['dynamic_sku']['color']) ? $item['attributes']['dynamic_sku']['color'] : [];
                $input['size'] = isset($item['attributes']['dynamic_sku']['size']) ? $item['attributes']['dynamic_sku']['size'] : [];
                $input['id'] = isset($item['attributes']['dynamic_sku']['id']) ? $item['attributes']['dynamic_sku']['id'] : [];
                $input['type'] = 'sku';
                $input['__model'] = 'GuoJiangClub\Component\Product\Models\Product';
            } else {
                $input['size'] = isset($item['size']) ? $item['size'] : '';
                $input['color'] = isset($item['color']) ? $item['color'] : '';
                $input['type'] = 'spu';
                $input['__model'] = 'GuoJiangClub\Component\Product\Models\Goods';
                if ($goods = Goods::find($item['id']) AND $goods->products()->count() > 0) {
                    throw new \Exception('请选择规格（颜色/尺码）后在下单');
                }
                /*eddy*/
                $input['id'] = $item['id'];
            }

            $data = new Item(array_merge($input), $item);

            $cartItems->put(md5(time() . $k), $data);

            return $cartItems;
        }
    }
}