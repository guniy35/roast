<?php

namespace GuoJiangClub\Component\Gift\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use GuoJiangClub\Component\Discount\Models\Discount;


class GiftCoupon extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['is_receive_coupon'];


    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'gift_coupon');

        parent::__construct($attributes);
    }


    public function coupon()
    {
        return $this->hasOne(Discount::class, 'id', 'coupon_id');
    }

    public function receive()
    {
        return $this->hasMany(GiftCouponReceive::class, 'gift_coupon_id');
    }

    public function getIsReceiveCouponAttribute()
    {
        if (count($this->receive) > 0) {
            return true;
        }
        return false;
    }


}

