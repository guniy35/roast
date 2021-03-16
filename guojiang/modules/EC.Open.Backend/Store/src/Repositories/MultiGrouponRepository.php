<?php


namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use Carbon\Carbon;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGroupon;
use Prettus\Repository\Eloquent\BaseRepository;

class MultiGrouponRepository extends BaseRepository
{
    public function model()
    {
        return MultiGroupon::class;
    }

    protected function createConditions()
    {
        $where = [];
        $or_where = [];
        $filter = [];
        $status = request('status');

        if ($title = request('title')) {
            $filter['title'] = ['like', '%' . $title . '%'];
        }

        if ($status == 'end') { //已结束
            $where['ends_at'] = ['<', Carbon::now()];
            $where['status'] = 1;
        } elseif ($status == 'on') {    //进行中
            $where['status'] = 1;
            $where['starts_at'] = ['<=', Carbon::now()];
            $where['ends_at'] = ['>=', Carbon::now()];
        } elseif ($status == 'future') {    //未开始
            $where['status'] = 1;
            $where['starts_at'] = ['>', Carbon::now()];
        } elseif ($status == 'invalid') {  //已失效
            $where['status'] = 0;
        } else {
            $where['status'] = ['<>', 2];
        }

        return [$where, $or_where, $filter];
    }

    /**
     * 秒杀活动分页数据
     * @param int $limit
     * @return mixed
     */
    public function getMultiGrouponPaginated($limit = 10)
    {
        $condition = $this->createConditions();
        $where = $condition[0];
        $or_where = $condition[1];
        $filter = $condition[2];

        $data = $this->scopeQuery(function ($query) use ($where, $or_where, $filter) {

            $query = $query->where(function ($query) use ($where, $or_where, $filter) {
                if (count($filter) > 0) {
                    foreach ($filter as $key => $value) {
                        if (is_array($value)) {
                            list($operate, $va) = $value;
                            $query = $query->where($key, $operate, $va);
                        } else {
                            $query = $query->where($key, $value);
                        }
                    }
                }

                $query->where(function ($query) use ($where, $or_where, $filter) {
                    if (count($where) > 0) {
                        foreach ($where as $key => $value) {
                            if (is_array($value)) {
                                list($operate, $va) = $value;
                                $query = $query->where($key, $operate, $va);
                            } else {
                                $query = $query->where($key, $value);
                            }
                        }
                    }
                    if (count($or_where) > 0) {
                        foreach ($or_where as $key => $value) {
                            if (is_array($value)) {
                                list($operate, $va) = $value;
                                $query = $query->orWhere($key, $operate, $va);
                            } else {
                                $query = $query->orWhere($key, $value);
                            }
                        }
                    }
                });
            });

            return $query->orderBy('created_at', 'desc');
        });

        if ($limit == 0) {
            return $data->all();
        } else {
            return $data->paginate($limit);
        }

    }
}