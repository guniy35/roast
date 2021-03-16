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

use GuoJiangClub\Component\Reduce\Models\ReduceUsers;
use Prettus\Repository\Eloquent\BaseRepository;

class ReduceUserRepository extends BaseRepository
{
    public function model()
    {
        return ReduceUsers::class;
    }

    public function getReduceUserByReduceID($user_id, $reduce_id)
    {
        return $this->with('reduceItem')->orderBy('id', 'desc')->findWhere(['user_id' => $user_id, 'reduce_id' => $reduce_id, 'is_leader' => 1])->first();
    }
}
