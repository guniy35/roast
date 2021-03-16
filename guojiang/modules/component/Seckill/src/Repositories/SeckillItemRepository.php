<?php

namespace GuoJiangClub\Component\Seckill\Repositories;

;
use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Seckill\Models\SeckillItem;
use Carbon\Carbon;
use GuoJiangClub\Component\Seckill\Models\SeckillSale;


class SeckillItemRepository extends BaseRepository
{

    const OPEN = 1;
    const CLOSE = 0;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SeckillItem::class;
    }


    /**
     * 获取全部未过期的有效秒杀活动列表
     * @param $limit
     * @return mixed
     */

    public function getSeckillItemAll($limit)
    {
        return $this->model
            ->where('status', self::OPEN)
            ->with(['seckill' => function ($query) {
                $query->where('status', self::OPEN);
            }])
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)->where('ends_at', '>=', Carbon::now());
            })
            ->with('goods')
            ->orderBy('sort', 'desc')
            ->paginate($limit);
    }

    /**
     * * 获取GoodsID获取有效秒杀活动信息
     * @param $goods_id
     * @return mixed
     */
    public function getSeckillItemByGoodsID($goods_id)
    {
        return $this->model
            ->where('status', self::OPEN)
            ->where('item_id', $goods_id)
            ->with(['seckill' => function ($query) {
                $query->where('status', self::OPEN);
            }])
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)->where('ends_at', '>=', Carbon::now());
            })
            ->first();

    }

    /**
     * 根据goods_id获取进行中的秒杀活动
     * @param $goods_id
     * @return mixed
     */
    public function getHavingSeckillByGoodsID($goods_id)
    {
        return $this->model
            ->where('status', self::OPEN)
            ->where('item_id', $goods_id)
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)->where('ends_at', '>=', Carbon::now())->where('starts_at', '<=', Carbon::now());
            })
            ->first();
    }


    /**
     * * 获取ID获取进行中的秒杀活动信息
     * @param $id
     * @return mixed
     */
    public function getSeckillItemByID($id)
    {
        return $this->model
            ->where('status', self::OPEN)
            ->where('id', $id)
            ->with(['seckill' => function ($query) {
                $query->where('status', self::OPEN);
            }])
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)->where('ends_at', '>=', Carbon::now())->where('starts_at', '<=', Carbon::now());
            })
            ->first();
    }


    public function CheckSeckillItemInfo($id, $seckill_price, $item_id)
    {
        return $this->model
            ->where('status', self::OPEN)
            ->where('id', $id)
            ->where('item_id', $item_id)
            ->where('seckill_price', $seckill_price)
            ->with(['seckill' => function ($query) {
                $query->where('status', self::OPEN);
            }])
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)->where('ends_at', '>=', Carbon::now())->where('starts_at', '<=', Carbon::now());
            })
            ->first();

    }


    public function getUserSeckillGoodsCountByItemId($seckill_item_id, $user_id)
    {
        if ($seckill_sale = SeckillSale::where('user_id', $user_id)->where('seckill_item_id', $seckill_item_id)->first()) {
            return $seckill_sale->quantity;
        }

        return 0;

    }

    /**
     * 获取第一条秒杀数据
     * @return mixed
     */
    public function getSeckillItemFirst()
    {
        return $this->model
            ->where('status', self::OPEN)
            ->where('recommend', 1)
            ->with(['seckill' => function ($query) {
                $query->where('status', self::OPEN);
            }])
            ->whereHas('seckill', function ($query) {
                return $query->where('status', self::OPEN)
                    ->where('ends_at', '>=', Carbon::now());
            })
            ->with('goods')
            ->first();
    }


}
