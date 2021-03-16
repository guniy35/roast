<?php

namespace GuoJiangClub\Component\Gift\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use GuoJiangClub\Component\Discount\Models\Discount;


class GiftDirectionalCoupon extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'gift_directional_coupon');

        parent::__construct($attributes);
    }


    public function coupon()
    {
        return $this->hasOne(Discount::class, 'id', 'coupon_id');
    }

    public function receive()
    {
        return $this->hasMany(GiftCouponReceive::class, 'type_id')->where('type', 'gift_directional_coupon');
    }

}

