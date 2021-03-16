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

use GuoJiangClub\Component\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;

class MultiGrouponUsers extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'multi_groupon_users');

        parent::__construct($attributes);
    }

    public function belongsToGroupon()
    {
        return $this->belongsTo(MultiGroupon::class, 'multi_groupon_id');
    }

    public function grouponItem()
    {
        return $this->belongsTo(MultiGrouponItems::class, 'multi_groupon_items_id')->withDefault();
    }

    public function setMetaAttribute($value)
    {
        $value['nick_name'] = 'base64:'.base64_encode($value['nick_name']);
        $this->attributes['meta'] = json_encode($value);
    }

    public function getMetaAttribute()
    {
        $meta = json_decode($this->attributes['meta'], true);
        $meta['nick_name'] = base64_decode(str_replace('base64:', '', $meta['nick_name']));

        return $meta;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
