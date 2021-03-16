<?php


namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;


use GuoJiangClub\EC\Open\Backend\Store\Model\ReduceUsers;

use Prettus\Repository\Eloquent\BaseRepository;

class ReduceUsersRepository extends BaseRepository
{
    public function model()
    {
        return ReduceUsers::class;
    }

    public function getReduceUsersPaginated($limit = 10)
    {
        $where['reduce_items_id'] = request('reduce_items_id');

        return $this->scopeQuery(function ($query) use ($where) {
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    list($condition, $val) = $value;
                    $query = $query->where($field, $condition, $val);
                } else {
                    $query = $query->where($field, '=', $value);
                }
            }

            if (request('mobile')) {

                return $query->whereHas('userInfo', function ($query) {

                    return $query->where('mobile', 'like', '%' . request('mobile') . '%');

                })->orderBy('created_at','asc');

            } else {

                return $query->orderBy('created_at','asc');
            }

        })->paginate($limit);
    }
}