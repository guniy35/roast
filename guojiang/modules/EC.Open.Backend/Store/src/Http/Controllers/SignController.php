<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\EC\Open\Backend\Store\Model\ElDiscount;
use GuoJiangClub\EC\Open\Backend\Store\Model\Sign;
use GuoJiangClub\EC\Open\Backend\Store\Model\SignReward;
use Illuminate\Http\Request;
use Validator;
use DB;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class SignController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $markets = Sign::orderby('created_at', 'desc')->paginate(10);

        return LaravelAdmin::content(function (Content $content) use ($markets) {

            $content->header('签到事件');

            $content->breadcrumb(
                ['text' => '签到事件管理', 'url' => '', 'no-pjax' => 1],
                ['text' => '签到事件', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '签到事件管理']

            );

            $content->body(view('store-backend::sign.index', compact('markets')));
        });
    }

    public function create()
    {
        $coupons = ElDiscount::where('status', 1)
            ->where('coupon_based', 1)
            ->where('starts_at', '<', Carbon::now())
            ->where('ends_at', '>', Carbon::now())->get();

        return LaravelAdmin::content(function (Content $content) use ($coupons) {

            $content->header('创建签到事件');

            $content->breadcrumb(
                ['text' => '事件管理', 'url' => 'store/marketing/sign', 'no-pjax' => 1],
                ['text' => '创建签到事件', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '签到事件管理']

            );

            $content->body(view('store-backend::sign.create', compact('coupons')));
        });
    }

    public function edit($id)
    {
        $sign = Sign::find($id);
        $coupons = ElDiscount::where('status', 1)
            ->where('coupon_based', 1)
            ->where('starts_at', '<', Carbon::now())
            ->where('ends_at', '>', Carbon::now())->get();
        $action = $sign->action;

        return LaravelAdmin::content(function (Content $content) use ($sign, $action, $coupons) {

            $content->header('修改签到事件');

            $content->breadcrumb(
                ['text' => '签到事件管理', 'url' => 'store/marketing/sign', 'no-pjax' => 1],
                ['text' => '修改签到事件', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '签到事件管理']

            );

            $content->body(view('store-backend::sign.edit', compact('sign', 'coupons', 'action')));
        });

    }

    public function store(Request $request)
    {
        $input = $request->except(['_token', 'id', 'deleteReward']);
        $base = $request->only(['title', 'label', 'share_text', 'status']);

        if (!$input['title']) {
            return $this->ajaxJson(false, [], 404, '请设置活动标题');
        }

        if (!isset($input['action'])) {
            return $this->ajaxJson(false, [], 404, '请设置连续签到');
        }

        if (!isset($input['reward'])) {
            return $this->ajaxJson(false, [], 404, '请设置签到抽奖奖品');
        }

        foreach ($input['action'] as $key => $item) {
            if (!$item['value']) {
                return $this->ajaxJson(false, [], 404, '请设置连续签到时间');
            }
        }

        foreach ($input['reward'] as $key => $item) {
            if (isset($item['value']) AND !$item['value']) {
                return $this->ajaxJson(false, [], 404, '请设置签到抽奖奖品');
            }
        }

        $rule = [];
        $action = [];
        foreach ($input['action'] as $key => $item) {
            $rule[] = $item['value'];
            $action[] = ['point' => $item['point'], 'coupon' => $item['coupon']];
        }

        $reward = [];
        $updateReward = [];
        foreach ($input['reward'] as $key => $item) {
            $label = '';
            if (isset($item['label'])) {
                $label = $item['label'];
            } elseif ($item['type'] == 'point') {
                $label = $item['value'] . '个积分';
            }

            $rewardData = [
                'type' => $item['type'],
                'type_value' => isset($item['value']) ? $item['value'] : 0,
                'label' => $label
            ];
            if (isset($item['id'])) {
                $updateReward[$item['id']] = $rewardData;
            } else {
                $reward[] = $rewardData;
            }
        }

        try {
            DB::beginTransaction();
            $base['rules'] = $rule;
            $base['action'] = $action;

            if ($id = request('id')) {
                $sign = Sign::find($id);
                $sign->fill($base);
                $sign->save();

                if (count($updateReward) > 0) {
                    foreach ($updateReward as $key => $item) {
                        $signReward = SignReward::find($key);
                        $signReward->fill($item);
                        $signReward->save();
                    }
                }
                $deleteIds = explode(',', $request->input('deleteReward'));
                if (count($deleteIds) > 0) {
                    SignReward::destroy($deleteIds);
                }


            } else {
                $sign = Sign::create($base);
            }

            $sign->rewards()->createMany($reward);
            DB::commit();

            return $this->ajaxJson(true, [], 0, '');

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception);
            return $this->ajaxJson(false, [], 404, '保存失败');
        }

    }

    public function delete($id)
    {
        if (!$sign = Sign::find($id)) {
            return $this->ajaxJson(false);
        }
        if ($sign->delete()) {
            return $this->ajaxJson(true);
        }

        return $this->ajaxJson(false);
    }


}
