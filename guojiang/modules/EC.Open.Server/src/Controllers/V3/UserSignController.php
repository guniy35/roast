<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/12
 * Time: 18:05
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\Component\Marketing\Models\Sign;
use GuoJiangClub\Component\Marketing\Repositories\SignItemRepository;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use Illuminate\Support\Carbon;


class UserSignController extends Controller
{
    protected $pointRepository;
    protected $signItemRepository;

    public function __construct(PointRepository $pointRepository,
                                SignItemRepository $signItemRepository
    )
    {
        $this->pointRepository = $pointRepository;
        $this->signItemRepository = $signItemRepository;
    }

    /**
     * 获取签到抽奖选项
     * @return \Dingo\Api\Http\Response
     */
    public function getSignReward()
    {
        $user = request()->user();
        $point = $this->pointRepository->getSumPointValid($user->id);

        $sign = Sign::where('status', 1)->first();

        if (!$sign) {
            return $this->failed('签到失败');
        }

        $reward = $sign->rewards->shuffle();
        $sign->sortReward = $reward;
        $sign->days = 0;
        $sign->get_reward = false;

        $item = $this->signItemRepository->getRunningSignByDay($user->id, 1); //如果今天未签到，获取前一天签到数据

        $is_sign = false;
        if ($this->signItemRepository->getCurrentSign($user->id)) {
            $is_sign = true;
            $item = $this->signItemRepository->getRunningSignByDay($user->id, 0); //当天签到数据
            if ($item->reward_status) {
                $sign->get_reward = true;

                //如果已抽奖，返回抽奖的顺序，中奖状态
                $sign_reward = $item->sign_reward;
                foreach ($sign_reward as $key => $value) {
                    if ($item->reward_id == $value['id']) {
                        $sign_reward[$key]['status'] = true;
                    } else {
                        $sign_reward[$key]['status'] = false;
                    }
                }
                $sign->sortReward = $sign_reward;
            }
        }

        if ($item) {
            $sign->days = $item->days; //已签到天数
        }

        return $this->success(compact('sign', 'item', 'point', 'is_sign'));
    }

    /**
     * 签到动作
     * @return \Dingo\Api\Http\Response
     */
    public function doSign()
    {
        $user = request()->user();
        if ($currentSign = $this->signItemRepository->getRunningSignByDay($user->id, 0)) {
            return $this->failed('您已签到');
        }

        $sumPointToday = $this->pointRepository->getDailySumByAction($user->id, 'daily_sign');
        if ($sumPointToday > 0) {
            return $this->failed('您已签到');
        }

        $signID = request('sign_id');

        $days = 1;
        if ($beforeSign = $this->signItemRepository->getRunningSignByDay($user->id, 1)) {
            $days = $beforeSign->days > 6 ? 1 : $beforeSign->days + 1;
        }

        $signItem = $this->signItemRepository->create([
            'user_id' => $user->id,
            'sign_id' => $signID,
            'sign_time' => Carbon::now(),
            'days' => $days
        ]);

        $sign = Sign::find($signItem->sign_id);
        $ruleKey = array_search($days, $sign->rules);
        if ($ruleKey === false) {
            $ruleKey = 0;
        }
        $action = $sign->action[$ruleKey];
        $message = '';
        foreach ($action as $key => $item) {
            if ($key == 'point' AND $item) {
                $message = '获得' . $item . '个积分;';
            }

            if ($key == 'coupon' AND $item) {
                $message = $message . '优惠券一张';
            }
        }

        event('sign.done', [$user->id, $signItem, $sign]);

        return $this->success(compact('item', 'message'));
    }

    /**
     * 抽奖动作
     * @return \Dingo\Api\Http\Response
     */
    public function doDraw()
    {
        $user = request()->user();
        $currentSign = $this->signItemRepository->getRunningSignByDay($user->id, 0);
        if ($currentSign->reward_status) {
            return $this->api([], false, 404, '您已抽奖');
        }
        $sign_reward = request('sign_reward');
        $reward_id = request('reward_id');

        foreach ($sign_reward as $key => $item) {
            if (!isset($item['id'])) {
                unset($sign_reward[$key]);
            }
        }
        $sign_reward = array_merge($sign_reward);


        foreach ($sign_reward as $key => $item) {
            if ($item['id'] == $reward_id) {
                $sign_reward[$key]['status'] = true;
            }
        }

        $currentSign->reward_status = 1;
        $currentSign->sign_reward = $sign_reward;
        $currentSign->reward_id = $reward_id;
        $currentSign->save();

        event('sign.draw', [$reward_id, $currentSign->id, $user->id]);

        return $this->api();

    }

}