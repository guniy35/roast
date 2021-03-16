<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Schedule;

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon;
use GuoJiangClub\Component\MultiGroupon\Models\PromotionGoodsRelation;
use iBrand\Scheduling\Scheduling;

class PromotionSchedule extends Scheduling
{
    public function schedule()
    {
        $this->schedule->call(function () {
            $table = config('ibrand.app.database.prefix', 'ibrand_').'promotion_goods_relation';
            $promotion = DB::table($table)
                ->select('origin_id', 'origin_type')
                ->distinct()
                ->get();

            if (count($promotion) > 0) {
                foreach ($promotion as $item) {
                    if ('multiGroupon' == $item->origin_type) {
                        if ($groupon = MultiGroupon::find($item->origin_id) and
                            (1 != $groupon->status or $groupon->ends_at < Carbon::now()->addHours(-24))
                        ) {
                            PromotionGoodsRelation::where('origin_id', $item->origin_id)->where('origin_type', 'multiGroupon')->delete();
                        }
                    }
                }
            }
        })->twiceDaily(12, 22);
    }
}
