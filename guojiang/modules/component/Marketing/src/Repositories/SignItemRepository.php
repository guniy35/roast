<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/12
 * Time: 18:29
 */

namespace GuoJiangClub\Component\Marketing\Repositories;


use Prettus\Repository\Contracts\RepositoryInterface;

interface SignItemRepository extends RepositoryInterface
{
    /**
     * 获取第前N天签到数据
     * @param $user_id
     * @param $day
     * @return mixed
     */
    public function getRunningSignByDay($user_id, $day);

    /**
     * 今天是否签到
     * @param $user_id
     * @return mixed
     */
    public function getCurrentSign($user_id);
}