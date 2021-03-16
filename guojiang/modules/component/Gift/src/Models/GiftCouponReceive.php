<?php

namespace GuoJiangClub\Component\Gift\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use GuoJiangClub\Component\Discount\Models\Coupon;
use GuoJiangClub\Component\User\Models\User;


class GiftCouponReceive extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'gift_coupon_receive');

        parent::__construct($attributes);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'discount_id', 'discount_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

