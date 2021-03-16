<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Component\Gift\Repositories\DiscountRepository;
use GuoJiangClub\Component\Gift\Repositories\GiftCouponRepository;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Gift\Models\GiftActivity;
use Carbon\Carbon;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use DB;

class GiftNewUserController extends Controller
{
    protected $discountRepository;

    protected $giftCouponRepository;

    protected $giftActivityRepository;

    public function __construct(
        DiscountRepository $discountRepository,
        GiftCouponRepository $giftCouponRepository,
        GiftActivityRepository $giftActivityRepository

    )
    {
        $this->discountRepository = $discountRepository;
        $this->giftCouponRepository = $giftCouponRepository;
        $this->giftActivityRepository = $giftActivityRepository;

    }

    public function index()
    {

        $condition = $this->getCondition();
        $coupons = [];
        $where = $condition[0];
        $orWhere = $condition[1];
        $where['status'] = 1;
        $where['starts_at'] = ['<=', Carbon::now()];
        $where['ends_at'] = ['>', Carbon::now()];

        $coupons = $this->discountRepository->getDiscountLists($where, $orWhere);
        if (count($coupons) > 0) {
            $coupons = $coupons->pluck('id')->toArray();
        }
        $lists = $this->giftActivityRepository->giftAll('gift_new_user');

        return LaravelAdmin::content(function (Content $content) use ($lists, $coupons) {

            $content->header('新人进店礼');

            $content->breadcrumb(
                ['text' => '新人进店礼', 'url' => 'store/promotion/gif/new_user', 'no-pjax' => 1],
                ['text' => '新人进店礼活动列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '新人进店礼']

            );

            $content->body(view('store-backend::gift_new_user.index', compact('lists', 'coupons')));
        });

    }

    public function create()
    {
        $s_time = '';
        $e_time = '';
        if ($list = $this->giftActivityRepository->giftListEffective('gift_new_user')) {
            $s_time = date('Y-m-d H:i:s', strtotime($list->ends_at) + 1);
            $e_time = date('Y-m-d H:i:s', strtotime("$s_time +30 day"));
        }

        return LaravelAdmin::content(function (Content $content) use ($s_time, $e_time) {

            $content->header('新人进店礼');

            $content->breadcrumb(
                ['text' => '新人进店礼', 'url' => 'store/promotion/gif/new_user', 'no-pjax' => 1],
                ['text' => '新建活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '新人进店礼']

            );

            $content->body(view('store-backend::gift_new_user.create', compact('s_time', 'e_time')));
        });

    }


    public function edit($id)
    {
        $gift_new_user = $this->giftActivityRepository->getListByID($id, 'gift_new_user');
        $coupon = [];
        if (count($gift_new_user->gift) > 0) {
            foreach ($gift_new_user->gift as $k => $item) {
                $coupon [$k] = $item->coupon->id;
            }
        }
        $coupon = json_encode($coupon, true);

        return LaravelAdmin::content(function (Content $content) use ($gift_new_user, $coupon) {

            $content->header('编辑活动');

            $content->breadcrumb(
                ['text' => '新人进店礼', 'url' => 'store/promotion/gif/new_user', 'no-pjax' => 1],
                ['text' => '编辑活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '新人进店礼']

            );

            $content->body(view('store-backend::gift_new_user.edit', compact('gift_new_user', 'coupon')));
        });

    }


    public function update($id)
    {
        $gift_new_user = $this->giftActivityRepository->getListByID($id, 'gift_new_user');
        if ($gift_new_user->status_text_new_user == '已过期失效') {
            return $this->ajaxJson(false, [], 400, '活动已过期失效保存失败');
        }

        $coupon = [];
        $input = request()->except(['_token', 'coupon', 'coupon_title']);
        $coupon_id = request('coupon');


        $input['point'] = !empty($input['point']) ? $input['point'] : 0;
        try {
            DB::beginTransaction();
            $res = $this->giftActivityRepository->update($input, $id);
            if ($input['open_coupon'] && count($coupon_id) > 0) {
                $this->giftCouponRepository->deleteWhere(['type_id' => $id, 'type' => 'gift_new_user']);
                foreach ($coupon_id as $item) {
                    if (!empty($item)) {
                        $coupon['type'] = $input['type'];
                        $coupon['type_id'] = $res->id;
                        $coupon['coupon_id'] = intval($item);
                        $coupon['num'] = 1;
                        $coupon['status'] = 1;
                        $this->giftCouponRepository->create($coupon);
                    }
                }
            }
            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage() . $exception->getTraceAsString());
            return $this->ajaxJson(false, [], 400, '');
        }
        return $this->ajaxJson(true, [], 200, '');

    }


    public function store()
    {
        $coupon = [];
        $input = request()->except(['_token', 'coupon', 'coupon_title']);
        $coupon_id = request('coupon');

        $input['point'] = !empty($input['point']) ? $input['point'] : 0;
        if ($list = $this->giftActivityRepository->giftListEffective('gift_new_user') && !$this->giftActivityRepository->checkAllowCreateGiftNewUser($input['starts_at'])) {
            return $this->ajaxJson(false, [], 400, '');
        };

        try {
            DB::beginTransaction();
            $res = $this->giftActivityRepository->create($input);
            if ($input['open_coupon'] && count($coupon_id) > 0) {
                foreach ($coupon_id as $item) {
                    if (!empty($item)) {
                        $coupon['type'] = $input['type'];
                        $coupon['type_id'] = $res->id;
                        $coupon['coupon_id'] = intval($item);
                        $coupon['num'] = 1;
                        $coupon['status'] = 1;
                        $this->giftCouponRepository->create($coupon);
                    }
                }
            }
            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage() . $exception->getTraceAsString());
            return $this->ajaxJson(false, [], 400, '');
        }
        return $this->ajaxJson(true, [], 200, '');
    }


    public function coupon_api()
    {
        $condition = $this->getCondition();
        $where = $condition[0];
        $orWhere = $condition[1];

        $coupons = $this->discountRepository->getDiscountLists($where, $orWhere);
        return $this->ajaxJson(true, $coupons, 200, '');
    }


    /**
     * 获取筛选条件
     * @return array
     */
    private function getCondition()
    {
        $where['coupon_based'] = 1;
        $orWhere = [];
        $status = request('status');
        if ($status == 'nstart') {
            $where['status'] = 1;
            $where['starts_at'] = ['>', Carbon::now()];
        }

        if ($status == 'ing') {
            $where['status'] = 1;
            $where['starts_at'] = ['<=', Carbon::now()];
            $where['ends_at'] = ['>', Carbon::now()];
        }

        if ($status == 'end') {
            $where['ends_at'] = ['<', Carbon::now()];

            $orWhere['coupon_based'] = 1;
            $orWhere['status'] = 0;
        }

        if (request('title') != '') {
            $where['title'] = ['like', '%' . request('title') . '%'];
        }

        return [$where, $orWhere];
    }


    public function destroy()
    {
        $id = request('id');
        $this->giftActivityRepository->delete($id);
        return $this->ajaxJson(true, [], 200, '');
    }


    public function toggleStatus()
    {

        $date = request('date');
        $status = request('status');
        $id = request('aid');
        $item = $this->giftActivityRepository->findWhere(['id' => $id])->first();
        if (Carbon::now() > $item->ends_at AND $status != 0) {
            return $this->ajaxJson(false, [], 400, '启动失败,活动已过期');
        }

        if ($status && !$this->giftActivityRepository->checkAllowCreateGiftNewUser($date)) {
            return $this->ajaxJson(false, [], 400, '启动失败,请先关闭已启动的活动');
        };

        if ($item) {
            $user = GiftActivity::find($id);
            $user->status = $status;
            $user->save();
            return $this->ajaxJson(true, [], 200, '');
        }
        return $this->ajaxJson(false, [], 400, '操作失败');
    }


    private function getListWhere()
    {
        $time = [];
        $where = [];

        if ($id = request('id')) {
            $where['recharge_rule_id'] = $id;
        }

        if ($order_no = request('order_no')) {
            $where['order_no'] = ['like', '%' . request('order_no') . '%'];
        }


        if (!empty(request('mobile'))) {
            $where['mobile'] = ['like', '%' . request('mobile') . '%'];
        }


        if (!empty(request('etime')) && !empty(request('stime'))) {
            $where['pay_time'] = ['<=', request('etime')];
            $time['pay_time'] = ['>=', request('stime')];
        }

        if (!empty(request('etime'))) {
            $time['pay_time'] = ['<=', request('etime')];
        }

        if (!empty(request('stime'))) {
            $time['pay_time'] = ['>=', request('stime')];
        }

        return [$time, $where];
    }

}