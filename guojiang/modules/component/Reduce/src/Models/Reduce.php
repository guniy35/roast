<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Models;

use Carbon\Carbon;
use GuoJiangClub\Component\Product\Models\Goods;
use Illuminate\Database\Eloquent\Model;

class Reduce extends Model
{
    const STATUS_END = 0;    //无效
    const STATUS_NEW = 1;    //有效

    protected $guarded = ['id'];

    protected $appends = ['server_time', 'status_text', 'status_init', 'reduce_total', 'store_nums'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'reduce');

        parent::__construct($attributes);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function users()
    {
        return $this->hasMany(ReduceUsers::class);
    }

    public function items()
    {
        return $this->hasMany(ReduceItems::class);
    }

    /**
     * 允许砍多少钱.
     *
     * @return mixed
     */
    public function getReduceTotalAttribute()
    {
        return sprintf('%01.2f', round($this->goods->sell_price - $this->price, 2));
    }

    /**
     * 时时库存.
     *
     * @return mixed
     */
    public function getStoreNumsAttribute()
    {
        $count = $this->items()->whereNotNull('order_id')->whereNotNull('complete_time')->count();

        $num = $this->reduce_store_nums - $count;

        if ($this->reduce_store_nums <= 0 || $num <= 0) {
            $this->ends_at = Carbon::now()->subMinute(1)->toDateTimeString();

            $this->save();

            return $num;
        }

        return $num;
    }

    public function getServerTimeAttribute()
    {
        return date('Y-m-d H:i:s', Carbon::now()->timestamp);
    }

    public function getStatusTextAttribute()
    {
        if ($this->store_nums > 0 and Reduce::STATUS_NEW == $this->status and $this->ends_at > Carbon::now() and $this->starts_at <= Carbon::now()) {
            return '进行中';
        } elseif ($this->store_nums > 0 and Reduce::STATUS_NEW == $this->status and $this->starts_at > Carbon::now()) {
            return '未开始';
        } elseif ($this->ends_at < Carbon::now() and Reduce::STATUS_NEW == $this->status) {
            return '已结束';
        }

        return '已失效';
    }

    public function getStatusInitAttribute()
    {
        if ($this->store_nums > 0 and Reduce::STATUS_NEW == $this->status and $this->ends_at > Carbon::now() and $this->starts_at <= Carbon::now()) {
            return 1;
        } elseif ($this->store_nums > 0 and Reduce::STATUS_NEW == $this->status and $this->starts_at > Carbon::now()) {
            return 2;
        } elseif ($this->ends_at < Carbon::now() and Reduce::STATUS_NEW == $this->status) {
            return 0;
        }

        return -1;
    }

    public function getEditStatusAttribute()
    {
        $status = 0;
        if ((1 == $this->status and $this->ends_at > Carbon::now() and $this->starts_at <= Carbon::now()) or
            (1 == $this->status and $this->starts_at > Carbon::now())
        ) {
            $status = 1;
        }

        return $status;
    }
}
