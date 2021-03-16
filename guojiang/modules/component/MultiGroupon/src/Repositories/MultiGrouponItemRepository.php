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
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponItems;
use Prettus\Repository\Eloquent\BaseRepository;

class MultiGrouponItemRepository extends BaseRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return MultiGrouponItems::class;
    }

    /**
     * 获取未成团的items.
     *
     * @return array
     */
    public function getGrouponItemList($user = null, $multiGrouponID, $multiGrouponItemsID, $limit = 8)
    {
        $where = [
            'status' => 0,
            'multi_groupon_id' => $multiGrouponID,
        ];

        $where['ends_at'] = ['>', Carbon::now()];
        if ($multiGrouponItemsID) {
            $where['id'] = ['<>', $multiGrouponItemsID];
        }

        return $this->scopeQuery(function ($query) use ($where) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    list($condition, $val) = $value;
                    $query = $query->where($field, $condition, $val);
                } else {
                    $query = $query->where($field, '=', $value);
                }
            }

            return $query = $query->whereHas('users', function ($query) {
                return $query->where('status', 1)->where('is_leader', 1);
            });
        })->paginate($limit);
    }

    public function getGrouponItemByID($id)
    {
        return $this->model
            ->with('groupon')
            ->with('groupon.goods')
            ->find($id);
    }
}
