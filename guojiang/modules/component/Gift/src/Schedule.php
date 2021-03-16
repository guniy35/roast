<?php

namespace GuoJiangClub\Component\Gift;

use Carbon\Carbon;
use iBrand\Scheduling\Scheduling;
use GuoJiangClub\Component\Point\Models\Point;
use GuoJiangClub\Component\Gift\Models\GiftActivity;
use DB;

class Schedule extends Scheduling
{
    public function schedule()
    {

        $this->schedule->call(function () {
            $birthday = $this->giftBirthday();
            $point_enabled = app('system_setting')->getSetting('point_enabled');

            $prefix = config('ibrand.app.database.prefix', 'ibrand_');

            $table=$prefix.'user';

            if ($birthday && $birthday->point && $point_enabled) {
                $time = Carbon::now()->timestamp;
                $date = date('Y-m-d', $time);
                $day = $birthday->activity_day - 1;
                $one = "SELECT * FROM $table WHERE 
          DATEDIFF(CAST(CONCAT(YEAR('$date'),DATE_FORMAT(birthday,'-%m-%d'))AS DATE),CAST(DATE_FORMAT('$date','%y-%m-%d') AS DATE)) BETWEEN 0 AND $day";
                $two = "DATEDIFF(CAST(CONCAT(YEAR('$date')+1,DATE_FORMAT(birthday,'-%m-%d'))AS DATE),CAST(DATE_FORMAT('$date','%y-%m-%d') AS DATE)) BETWEEN 0 AND $day";
                $user = DB::select($one . ' OR ' . $two);
                if (count($user) > 0) {
                    \Log::info('gift_birthday定时任务送积分');
                    $this->userGiftBirthdayPoint($user, $birthday);
                }

            }
        })->dailyAt('10:00');


    }

    private function giftBirthday()
    {
        return GiftActivity::where('type', 'gift_birthday')->where('status', 1)
            ->where('ends_at', '>=', Carbon::now())
            ->where('starts_at', '<=', Carbon::now())
            ->first();
    }


    private function userGiftBirthdayPoint($user, $activity)
    {
        try {
            foreach ($user as $item) {
                if ($item->name && $item->mobile && $item->birthday) {
                    $point_status = false;
                    $time = Carbon::now()->timestamp;
                    $birthday = date('Y-m-d', $time);
                    $point = Point::where('action', 'gift_birthday_point')
                        ->where('user_id', $item->user_id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    if ($point) {
                        if (intval(strtotime(date('Y-m-d', strtotime($point->created_at))) !== intval(strtotime($birthday)))) {
                            $point_status = true;
                        }
                    }

                    if ($point == null || $point_status) {
                        Point::create(['user_id' => $item->user_id, 'action' =>
                            'gift_birthday_point', 'note' => date('Y', $time) . '年生日礼赠送积分', 'item_type' => GiftActivity::class,
                            'item_id' => $activity->id
                            , 'value' => $activity->point]);
                    }
                }

            }
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
        }
    }

}