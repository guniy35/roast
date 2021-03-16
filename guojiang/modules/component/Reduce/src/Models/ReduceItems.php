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
use GuoJiangClub\Component\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;

class ReduceItems extends Model
{
    const STATUS_TEMP = 0;    //临时
    const STATUS_NEW = 1;    //生效
    const STATUS_END = 2;    //无效

    protected $hidden = ['reduce_amount_arr'];

    protected $guarded = ['id'];

    protected $appends = ['reduce_total', 'reduce_surplus_amount', 'reduce_amount', 'status_text', 'status_init', 'progress_par', 'user_is_leader', 'time_price', 'user'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'reduce_items');

        parent::__construct($attributes);
    }

    public function getReduceSurplusAmountAttribute()
    {
        return sprintf('%01.2f', round($this->reduce->reduce_total - $this->reduce_amount, 2));
    }

    /**
     * 已砍金额.
     *
     * @return int
     */
    public function getReduceAmountAttribute()
    {
        $all = $this->users->all();

        $reduce_amount = 0;

        if (count($all)) {
            foreach ($all as $item) {
                $reduce_amount += $item->reduce_amount;
            }
        }

        return sprintf('%01.2f', round($reduce_amount, 2));
    }

    public function getUserAttribute()
    {
        return $this->users->where('is_leader', 1)->first();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function users()
    {
        return $this->hasMany(ReduceUsers::class, 'reduce_items_id')->orderBy('reduce_amount', 'desc');
    }

    public function getTotalUser()
    {
        return $this->users()->where('status', 0)->count();
    }

    public function reduce()
    {
        return $this->belongsTo(Reduce::class, 'reduce_id');
    }

    public function getReduceTotalAttribute()
    {
        return sprintf('%01.2f', round($this->reduce->reduce_total, 2));
    }

    /**
     * 砍价是否正常进行中.
     *
     * @return int
     */
    public function getStatusTextAttribute()
    {
        if (ReduceItems::STATUS_END == $this->status) {
            return '已失效';
        } elseif ($this->reduce->store_nums <= 0) {
            return '已结束';
        } elseif ($this->reduce->store_nums > 0
            and ReduceItems::STATUS_TEMP == $this->status
            and $this->ends_at > Carbon::now()
            and $this->starts_at <= Carbon::now()) {
            return '进行中';
        } elseif (
            ReduceItems::STATUS_NEW == $this->status
            and 0 == $this->reduce_surplus_amount
            and !$this->order_id) {
            return '已完成待下单';
        } elseif (!$this->complete_time
            and ReduceItems::STATUS_NEW == $this->status
            and $this->order_id) {
            return '已下单待支付';
        } elseif ($this->complete_time
            and ReduceItems::STATUS_NEW == $this->status
            and $this->order_id) {
            return '订单已支付';
        } elseif ($this->ends_at < Carbon::now()
            and ReduceItems::STATUS_NEW == $this->status) {
            return '已结束';
        }

        return '已失效';
    }

    public function getStatusInitAttribute()
    {
        if (ReduceItems::STATUS_END == $this->status) {
            return -1;
        } elseif ($this->reduce->store_nums <= 0) {
            return 0;
        } elseif ($this->reduce->store_nums > 0
            and ReduceItems::STATUS_TEMP == $this->status
            and $this->ends_at > Carbon::now()
            and $this->starts_at < Carbon::now()) {
            return 1;
        } elseif (
            ReduceItems::STATUS_NEW == $this->status
            and 0 == $this->reduce_surplus_amount
            and !$this->order_id) {
            return 2;
        } elseif (!$this->complete_time
            and ReduceItems::STATUS_NEW == $this->status
            and $this->order_id) {
            return 3;
        } elseif ($this->complete_time
            and ReduceItems::STATUS_NEW == $this->status
            and $this->order_id) {
            return 4;
        } elseif ($this->ends_at < Carbon::now()
            and ReduceItems::STATUS_NEW == $this->status) {
            return 0;
        }

        return -1;
    }

    public function getProgressParAttribute()
    {
        return sprintf('%01.2f', round($this->reduce_amount / $this->reduce->reduce_total, 2));
    }

    public function getUserIsLeaderAttribute()
    {
        $user = auth('api')->user();

        if ($user and $user->id == $this->user_id) {
            return 1;
        }

        return 0;
    }

    /**
     * 时时价格=商品价格-已砍金额.
     *
     * @return int
     */
    public function getTimePriceAttribute()
    {
        $price = $this->reduce->goods->sell_price - $this->reduce_amount;

        if ($this->reduce->price >= $price) {
            return sprintf('%01.2f', round($this->reduce->price, 2));
        }

        return sprintf('%01.2f', round($price, 2));
    }

    public function getEndsAtAttribute()
    {
        $ends_at = $this->attributes['ends_at'];

        if ($this->complete_time) {
            return $this->complete_time;
        }

        return $ends_at;
    }
}
