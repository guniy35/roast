<?php

namespace GuoJiangClub\Component\Seckill\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Seckill\Models\Seckill;
use Carbon\Carbon;

class SeckillRepository extends BaseRepository
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
        return Seckill::class;
    }


    /**
     * 通过秒杀活动ID获取进行中有效秒杀信息
     * @param $id
     * @return array
     */
    public function getSeckillById($id)
    {
        return $this->model->where('ends_at', '>=', Carbon::now())
            ->where('starts_at', '<=', Carbon::now())
            ->where('status', self::OPEN)
            ->with('items.goods')
            ->with(['items' => function ($query) {
                $query->where('status', self::OPEN)->orderBy('id', 'desc');
            }])
            ->whereHas('items', function ($query) use ($id) {
                return $query->where('seckill_id', $id);
            })->first();
    }


    /**
     * 获取全部进行中有效秒杀信息
     * @return array
     */
    public function getSeckillAll()
    {
        return $this->model
            ->orderBy('starts_at', 'asc')
            ->where('ends_at', '>=', Carbon::now())
            ->where('starts_at', '<=', Carbon::now())
            ->where('status', self::OPEN)
            ->with('items.goods')
            ->with(['items' => function ($query) {
                $query->where('status', self::OPEN)->orderBy('id', 'desc');
            }])
            ->whereHas('items', function ($query) {
                return $query;
            })->get();
    }

}
