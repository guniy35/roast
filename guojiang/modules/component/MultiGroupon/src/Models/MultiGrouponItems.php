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

use Illuminate\Database\Eloquent\Model;

class MultiGrouponItems extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'multi_groupon_items');

        parent::__construct($attributes);
    }

    public function users()
    {
        return $this->hasMany(MultiGrouponUsers::class, 'multi_groupon_items_id');
    }

    public function getTotalUser()
    {
        return $this->users()->where('status', 1)->count();
    }

    public function groupon()
    {
        return $this->belongsTo(MultiGroupon::class, 'multi_groupon_id');
    }

    public function getGapNumber()
    {
        $gap = $this->groupon->number - $this->getTotalUser();
        if ($gap > 0) {
            return $gap;
        }

        return 0;
    }
}
