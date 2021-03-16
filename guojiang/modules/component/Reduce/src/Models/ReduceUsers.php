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

use Illuminate\Database\Eloquent\Model;

class ReduceUsers extends Model
{
    const STATUS_NEW = 0;    //有效
    const STATUS_END = 1;    //无效

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'reduce_users');

        parent::__construct($attributes);
    }

    public function reduce()
    {
        return $this->belongsTo(Reduce::class, 'reduce_id');
    }

    public function reduceItem()
    {
        return $this->belongsTo(ReduceItems::class, 'reduce_items_id')->withDefault();
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
}
