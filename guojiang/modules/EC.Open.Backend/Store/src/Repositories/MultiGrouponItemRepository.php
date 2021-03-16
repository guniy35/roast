<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;


use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGrouponItems;
use Prettus\Repository\Eloquent\BaseRepository;

class MultiGrouponItemRepository extends BaseRepository
{
    public function model()
    {
        return MultiGrouponItems::class;
    }

    /**
     * 根据团ID获取拼团失败的item
     * @param $groupon_id
     */
    public function getFailItemsByGrouponIdPaginate($groupon_id, $limit = 5)
    {
        $items = $this->scopeQuery(function ($query) use ($groupon_id) {
            return $query->where('status', 2)
                ->where('multi_groupon_id', $groupon_id);

        });
        if ($limit) {
            return $items->paginate($limit);
        }
        return $items->all();
    }

}