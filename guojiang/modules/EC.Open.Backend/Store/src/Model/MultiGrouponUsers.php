<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;


use GuoJiangClub\EC\Open\Backend\Store\Model\Relations\BelongToUserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultiGrouponUsers extends Model
{
    use BelongToUserTrait;

    protected $guarded = ['id'];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'multi_groupon_users');
    }


    public function getMetaAttribute()
    {
        $meta = json_decode($this->attributes['meta'], true);
        $meta['nick_name'] = base64_decode(str_replace('base64:', '', $meta['nick_name']));
        return $meta;
    }

    public function grouponItem()
    {
        return $this->belongsTo(MultiGrouponItems::class, 'multi_groupon_items_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
