<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiGrouponItems extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'multi_groupon_items');
    }


    public function users()
    {
        return $this->hasMany(MultiGrouponUsers::class);
    }

    public function getCountUser()
    {
        return $this->users()->where('status', 1)->count();
    }

    public function getLeader()
    {
        return $this->users()->where('is_leader', 1)->first();
    }

    public function groupon()
    {
        return $this->belongsTo(MultiGroupon::class, 'multi_groupon_id');
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 0:
                return '待成团';
                break;
            case 1:
                return '拼团成功';
                break;
            default:
                return '拼团失败';
        }
    }

}
