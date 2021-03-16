<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/15
 * Time: 16:29
 */

namespace GuoJiangClub\EC\Open\Backend\Store\Model;


use Illuminate\Database\Eloquent\Model;

class RefundAmount extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'refund_amount');
    }

    public function getTypeTextAttribute()
    {
        $text = '';
        switch ($this->attributes['type']) {
            case 'balance':
                return '系统自动退款到用户余额账户';
                break;
            case 'cash':
                return '现金';
                break;
        }
        return $text;
    }
}