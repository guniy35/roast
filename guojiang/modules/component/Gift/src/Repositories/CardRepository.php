<?php

namespace GuoJiangClub\Component\Gift\Repositories;

use GuoJiangClub\Component\User\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use DB;

class CardRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    public function getInstantBirthdayUserByDay($where, $old_day, $limit = 20, $all = false)
    {
        $prefix = config('ibrand.app.database.prefix', 'ibrand_');
        $table = $prefix . 'user';

        $time = Carbon::now()->timestamp;
        $date = date('Y-m-d', $time);
        $day = $old_day - 1;
        $one = "SELECT * FROM $table WHERE 
          DATEDIFF(CAST(CONCAT(YEAR('$date'),DATE_FORMAT(birthday,'-%m-%d'))AS DATE),CAST(DATE_FORMAT('$date','%y-%m-%d') AS DATE)) BETWEEN 0 AND $day";
        $two = "DATEDIFF(CAST(CONCAT(YEAR('$date')+1,DATE_FORMAT(birthday,'-%m-%d'))AS DATE),CAST(DATE_FORMAT('$date','%y-%m-%d') AS DATE)) BETWEEN 0 AND $day";
        $res = DB::select($one . ' OR ' . $two);
        if (count($res) <= 0) return [];

        $arr = collect($res)->pluck('id')->toArray();

        $date = $this->scopeQuery(function ($query) use ($where, $arr) {
            $query = $query->Where(function ($query) use ($where, $arr) {
                if (is_array($where)) {
                    foreach ($where as $key => $value) {
                        if (is_array($value)) {
                            list($operate, $va) = $value;
                            $query = $query->where($key, $operate, $va);
                        } else {
                            $query = $query->where($key, $value);
                        }
                    }
                }
            });

            $query->whereIn('id', $arr);

            return $query->where('mobile', '<>', '')->where('birthday', '<>', '')->orderBy('birthday', 'desc');
        });

        if ($all) {
            return $date->all();
        }
        return $date->paginate($limit);

    }


    public function checkUserBirthdayInUsers($user_id, $user)
    {
        if (count($user) > 0) {
            $arr = collect($user)->pluck('id')->toArray();
            if (in_array($user_id, $arr)) {
                return true;
            }
        }
        return false;
    }


}
