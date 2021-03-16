<?php

namespace GuoJiangClub\Component\Refund\Repositories\Eloquent;

use GuoJiangClub\Component\Refund\Models\Refund;
use GuoJiangClub\Component\Refund\Repositories\RefundRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class RefundRepositoryEloquent extends BaseRepository implements RefundRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Refund::class;
    }

    public function getRefundsByCriteria($andConditions, $orConditions, $limit = 15)
    {
        $query = $this->model;

        foreach ($andConditions as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $query = $query->where($this->model->getTable() . '.' . $field, $condition, $val);
            } elseif ($value == 'end') {
                $query = $query->whereIn($this->model->getTable() . '.' . 'status', [3, 4]);
            } elseif ($value == 'process') {
                $query = $query->whereNotIn($this->model->getTable() . '.' . 'status', [3, 4]);
            } else {
                $query = $query->where($this->model->getTable() . '.' . $field, '=', $value);
            }
        }

        if (count($orConditions) > 0) {
            $query = $query->orWhere(function ($query) use ($orConditions) {
                return $query->whereHas('order', function ($query) use ($orConditions) {
                    list($field, $condition, $val) = $orConditions['order_no'];

                    return $query = $query->where($field, $condition, $val);
                });
            });

            $query = $query->orWhere(function ($query) use ($orConditions) {
                return $query->whereHas('orderItem', function ($query) use ($orConditions) {
                    list($field, $condition, $val) = $orConditions['item_name'];

                    return $query = $query->where($field, $condition, $val);
                });
            });
        }

        return $query->orderBy('created_at', 'desc')->with('orderItem', 'order')->paginate($limit);
    }
}
