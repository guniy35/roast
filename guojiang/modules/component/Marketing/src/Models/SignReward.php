<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/11
 * Time: 19:49
 */

namespace GuoJiangClub\Component\Marketing\Models;


use Illuminate\Database\Eloquent\Model;

class SignReward extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'marketing_sign_reward');

        parent::__construct($attributes);
    }

}