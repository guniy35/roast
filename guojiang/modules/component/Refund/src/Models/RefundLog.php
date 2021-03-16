<?php

namespace GuoJiangClub\Component\Refund\Models;

use Illuminate\Database\Eloquent\Model;

class RefundLog extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'refund_log');
    }

    public function getRefundMsg(Refund $refund)
    {
        $type = $refund->refund_type;
        $this->RefundMsg = '';
    }
}
