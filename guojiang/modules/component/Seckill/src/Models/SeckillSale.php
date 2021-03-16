<?php

namespace GuoJiangClub\Component\Seckill\Models;

use Illuminate\Database\Eloquent\Model;

class SeckillSale extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'seckill_sale');

        parent::__construct($attributes);
    }
}