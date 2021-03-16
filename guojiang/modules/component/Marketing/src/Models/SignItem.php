<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 19:48
 */

namespace GuoJiangClub\Component\Marketing\Models;


use Illuminate\Database\Eloquent\Model;

class SignItem extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'marketing_sign_item');

        parent::__construct($attributes);
    }

    public function setSignRewardAttribute($value)
    {
        $this->attributes['sign_reward'] = json_encode($value);
    }

    public function getSignRewardAttribute()
    {
        return json_decode($this->attributes['sign_reward'], true);
    }

    public function sign()
    {
        return $this->belongsTo(Sign::class);
    }
}