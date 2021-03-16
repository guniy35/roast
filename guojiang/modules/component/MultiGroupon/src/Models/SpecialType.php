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
}
