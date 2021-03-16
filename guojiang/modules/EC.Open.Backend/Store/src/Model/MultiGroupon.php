<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MultiGroupon extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'multi_groupon');
    }


    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function items()
    {
        return $this->hasMany(MultiGrouponItems::class);
    }

    public function users()
    {
        return $this->hasMany(MultiGrouponUsers::class);
    }

    public function getCountUsers()
    {
        return $this->users()->where('status', 1)->count();
    }

    public function getStatusTextAttribute()
    {
        if ($this->status == 1 AND $this->ends_at > Carbon::now() AND $this->starts_at <= Carbon::now()) {
            return '进行中';
        } elseif ($this->status == 1 AND $this->starts_at > Carbon::now()) {
            return '未开始';
        } elseif ($this->ends_at < Carbon::now() AND $this->status == 1) {
            return '已结束';
        } else {
            return '已失效';
        }

    }

    public function getEditStatusAttribute()
    {
        $status = 0;
        if (($this->status == 1 AND $this->ends_at > Carbon::now() AND $this->starts_at <= Carbon::now()) OR
            ($this->status == 1 AND $this->starts_at > Carbon::now())
        ) {
            $status = 1;
        }
        return $status;
    }

}
