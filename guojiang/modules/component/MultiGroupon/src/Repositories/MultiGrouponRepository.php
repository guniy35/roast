<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Repositories;

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon;
use Prettus\Repository\Eloquent\BaseRepository;

class MultiGrouponRepository extends BaseRepository
{
    const OPEN = 1;
    const CLOSE = 0;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return MultiGroupon::class;
    }

    /**
     * 获取全部进行中有效团购信息.
     *
     * @return array
     */
    public function getGrouponList($limit = 0)
    {
        $model = $this->model
            ->where('ends_at', '>=', Carbon::now())
            ->where('starts_at', '<=', Carbon::now())
            ->where('status', self::OPEN)
            ->with('goods')
            ->orderBy('sort', 'desc');

        if ($limit) {
            return $model->paginate($limit);
        }

        return $model->get();
    }

    /**
     * 根据goodsID获取有效拼团数据.
     *
     * @param $goods_id
     *
     * @return mixed
     */
    public function getValidGroupByGoodsId($goods_id)
    {
        return $this->model
            ->where('ends_at', '>=', Carbon::now())
            ->where('status', self::OPEN)
            ->where('goods_id', $goods_id)
            ->first();
    }
}
