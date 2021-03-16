<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/12
 * Time: 18:30
 */

namespace GuoJiangClub\Component\Marketing\Repositories\Eloquent;


use GuoJiangClub\Component\Marketing\Models\SignItem;
use GuoJiangClub\Component\Marketing\Repositories\SignItemRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class SignItemRepositoryEloquent extends BaseRepository implements SignItemRepository
{
    public function model()
    {
        return SignItem::class;
    }

    public function getRunningSignByDay($user_id,$day)
    {
        return $this->scopeQuery(function ($query) use ($user_id,$day) {
            return $query->where(DB::raw('DATEDIFF(now(),sign_time)'), $day)
                ->where('user_id',$user_id);
        })->first();
    }

    public function getCurrentSign($user_id)
    {
        return $this->scopeQuery(function ($query) use ($user_id) {
            return $query->where(DB::raw('to_days(sign_time)'), DB::raw('to_days(now())'))
                ->where('user_id',$user_id);
        })->first();
    }
}