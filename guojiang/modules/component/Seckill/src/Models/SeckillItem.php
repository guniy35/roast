<?php

namespace GuoJiangClub\Component\Seckill\Models;

use Illuminate\Database\Eloquent\Model;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Order\Models\Order;
use Carbon\Carbon;

class SeckillItem extends Model
{
    const ING = 1;        //进行中
    const UNING = 2;      //未开始
    const INGED = 3;      //已过期

    protected $guarded = ['id'];

    protected $appends = ['starts_at', 'ends_at', 'init_status', 'is_end', 'auto_close', 'server_time'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'seckill_item');

        parent::__construct($attributes);
    }

    public function seckill()
    {
        return $this->hasOne(Seckill::class, 'id', 'seckill_id');
    }

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'item_id');
    }


    public function getStartsAtAttribute()
    {
        if (isset($this->seckill->starts_at)) {
            return $this->seckill->starts_at;
        }
        return '';
    }


    public function getServerTimeAttribute()
    {
        return date('Y-m-d H:i:s', Carbon::now()->timestamp);
    }


    public function getAutoCloseAttribute()
    {
        if (isset($this->seckill->auto_close)) {
            return $this->seckill->auto_close;
        }
        return 0;
    }


    public function getIsEndAttribute()
    {
        if (Carbon::now() >= $this->seckill->starts_at And Carbon::now() <= $this->seckill->ends_at) {
            return false;
        } elseif (Carbon::now() < $this->seckill->starts_at) {
            return false;
        }
        return true;
    }


    public function getEndsAtAttribute()
    {
        if (isset($this->seckill->ends_at)) {
            return $this->seckill->ends_at;
        }
        return '';
    }


    public function getInitStatusAttribute()
    {
        if (Carbon::now() >= $this->seckill->starts_at And Carbon::now() <= $this->seckill->ends_at) {
            return self::ING;
        } elseif (Carbon::now() < $this->seckill->starts_at) {
            return self::UNING;
        }
        return self::INGED;
    }

}