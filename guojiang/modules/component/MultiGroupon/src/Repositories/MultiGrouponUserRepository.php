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

use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use Prettus\Repository\Eloquent\BaseRepository;

class MultiGrouponUserRepository extends BaseRepository
{
    public function model()
    {
        return MultiGrouponUsers::class;
    }

    /**
     * 根据用户id，团id，子团id查询用户的参团信息.
     *
     * @param $user_id
     * @param $multi_groupon_id
     * @param $multi_groupon_items_id
     *
     * @return mixed
     */
    public function getGrouponUser($user_id, $multi_groupon_id, $multi_groupon_items_id)
    {
        return $this->findWhere(['user_id' => $user_id, 'multi_groupon_id' => $multi_groupon_id, 'multi_groupon_items_id' => $multi_groupon_items_id])->first();
    }

    /**
     * 根据团ID获取用户.
     *
     * @param $user_id
     * @param $multi_groupon_id
     *
     * @return mixed
     */
    public function getGrouponUserByGrouponID($user_id, $multi_groupon_id)
    {
        return $this->findWhere(['user_id' => $user_id, 'multi_groupon_id' => $multi_groupon_id, 'is_leader' => 1])->first();
    }

    public function getGrouponUserByCondition($where)
    {
        return $this->scopeQuery(function ($query) use ($where) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    list($condition, $val) = $value;
                    $query = $query->where($field, $condition, $val);
                } else {
                    $query = $query->where($field, '=', $value);
                }
            }

            return $query;
        })->first();
    }
}
