<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

use DB;

class AgentMembersTransformer extends BaseTransformer
{
    public function transformData($model)
    {
        $data = $model->toArray();

        //$user_group = DB::table('el_user_group')->find($data['user']['group_id']);

        //$data['grade'] = $user_group ? $user_group->grade : 0;

        return $data;
    }
}