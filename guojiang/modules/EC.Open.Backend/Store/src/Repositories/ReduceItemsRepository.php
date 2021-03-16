<?php


namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;


use GuoJiangClub\EC\Open\Backend\Store\Model\ReduceItems;

use Prettus\Repository\Eloquent\BaseRepository;

class ReduceItemsRepository extends BaseRepository
{
    public function model()
    {
        return ReduceItems::class;
    }

    public function getReduceItemsPaginated($limit = 10)
    {
        $where['reduce_id'] = request('reduce_id');


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

                return $query->with('order')->whereHas('userInfo', function ($query) {

                    return $query->where('mobile', 'like', '%' . request('mobile') . '%');

                })->orderBy('starts_at','desc');

            } else {

                return $query->with('order')->with('users')->orderBy('starts_at','desc');
            }

        })->paginate($limit);
    }
}