<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Models;

use Carbon\Carbon;
use GuoJiangClub\Component\Product\Models\Goods;
use Illuminate\Database\Eloquent\Model;

class MultiGroupon extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['init_status', 'server_time'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'multi_groupon');

        parent::__construct($attributes);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function users()
    {
        return $this->hasMany(MultiGrouponUsers::class);
    }

    /**
     * 拼团是否正常进行中.
     *
     * @return int
     */
    public function getInitStatusAttribute()
    {
        if ($this->starts_at <= Carbon::now() and 1 == $this->status) {  //活动已开始
            return 1;
        }

        if ($this->starts_at > Carbon::now() and 1 == $this->status) {  //活动未开始
            return 2;
        }

        return 0;
    }

    public function getServerTimeAttribute()
    {
        return date('Y-m-d H:i:s', Carbon::now()->timestamp);
    }

    public function getStatusTextAttribute()
    {
        if (1 == $this->status and $this->ends_at > Carbon::now() and $this->starts_at <= Carbon::now()) {
            return '进行中';
        } elseif (1 == $this->status and $this->starts_at > Carbon::now()) {
            return '未开始';
        } elseif ($this->ends_at < Carbon::now() and 1 == $this->status) {
            return '已结束';
        }

        return '已失效';
    }
}
