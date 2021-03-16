<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use GuoJiangClub\Component\Reduce\Models\ReduceItems as ComponentReduceItems;
use GuoJiangClub\Component\User\Models\User;

class ReduceItems extends ComponentReduceItems
{

    public function userInfo()
    {
        return $this->belongsTo(User::class,'user_id');

    }

}
