<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use GuoJiangClub\Component\Reduce\Models\ReduceUsers as ComponentReduceUsers;
use GuoJiangClub\Component\User\Models\User;

class ReduceUsers extends ComponentReduceUsers
{

    public function userInfo()
    {
        return $this->belongsTo(User::class,'user_id');

    }

}
