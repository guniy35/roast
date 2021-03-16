<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/12
 * Time: 12:26
 */

namespace GuoJiangClub\EC\Open\Backend\Store\Model;


class Sign extends \GuoJiangClub\Component\Marketing\Models\Sign
{    

    public function setRulesAttribute($value)
    {
        $this->attributes['rules'] = json_encode($value);
    }

    public function setActionAttribute($value)
    {
        $this->attributes['action'] = json_encode($value);
    }
    
    
   
}