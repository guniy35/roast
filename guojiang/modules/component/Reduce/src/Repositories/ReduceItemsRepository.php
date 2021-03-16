<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Repositories;

use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use Prettus\Repository\Eloquent\BaseRepository;

class ReduceItemsRepository extends BaseRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return ReduceItems::class;
    }

    public function getReduceItemByID($id)
    {
        return $this->model
            ->with('reduce')
            ->with('users')
            ->with('reduce.goods')
            ->with('order')
            ->find($id);
    }

    public function getReduceItemByUserID($user_id, $limit = 0)
    {
        $model = $this->model
            ->with('reduce')
            ->with('reduce.goods')
            ->with('order')
            ->where('user_id', $user_id)
            ->orderBy('id', 'desc');

        if ($limit) {
            return $model->paginate($limit);
        }

        return $model->get();
    }
}
