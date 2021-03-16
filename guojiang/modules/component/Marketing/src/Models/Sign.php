<?php
namespace GuoJiangClub\Component\Marketing\Models;


use Illuminate\Database\Eloquent\Model;


class Sign extends Model
{

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'marketing_sign');

        parent::__construct($attributes);
    }

    public function rewards()
    {
        return $this->hasMany(SignReward::class);
    }
    
    public function getRulesAttribute()
    {
        return json_decode($this->attributes['rules'],true);
    }

    public function getActionAttribute()
    {
        return json_decode($this->attributes['action'],true);
    }
}