<?php

/*
 * This file is part of ibrand/order.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Order\Models;

use GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon;
use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class SpecialType extends Model implements Transformable
{
    use SoftDeletes;

    use TransformableTrait;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'order_special_type');
        parent::__construct($attributes);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function multiGroupon()
    {
        return $this->belongsTo(MultiGroupon::class, 'origin_id');
    }

    public function reduceItems()
    {
        return $this->belongsTo(ReduceItems::class, 'origin_id');
    }
}
