<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\MerchantPay;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGroupon;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGrouponItems;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGrouponUsers;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\MultiGrouponItemRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\MultiGrouponRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\GoodsService;
use GuoJiangClub\EC\Open\Backend\Store\Service\MultiGrouponService;
use GuoJiangClub\EC\Open\Backend\Store\Service\SpecialGoodsService;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Validator;
use DB;


class MultiGrouponController extends Controller
{
    protected $multiGrouponRepository;
    protected $goodsService;
    protected $goodsRepository;
    protected $specialGoodsService;
    protected $multiGrouponService;
    protected $multiGrouponItemRepository;

    public function __construct(MultiGrouponRepository $multiGrouponRepository,
                                GoodsService $goodsService,
                                GoodsRepository $goodsRepository,
                                SpecialGoodsService $specialGoodsService,
                                MultiGrouponService $multiGrouponService,
                                MultiGrouponItemRepository $multiGrouponItemRepository)
    {
        $this->multiGrouponRepository = $multiGrouponRepository;
        $this->goodsService = $goodsService;
        $this->goodsRepository = $goodsRepository;
        $this->specialGoodsService = $specialGoodsService;
        $this->multiGrouponService = $multiGrouponService;
        $this->multiGrouponItemRepository = $multiGrouponItemRepository;
    }

    public function index()
    {

        $grouponList = $this->multiGrouponRepository->getMultiGrouponPaginated();

        return LaravelAdmin::content(function (Content $content) use ($grouponList) {

            $content->header('????????????????????????');

            $content->breadcrumb(
                ['text' => '??????????????????', 'url' => 'store/promotion/multiGroupon', 'no-pjax' => 1],
                ['text' => '????????????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

            );

            $content->body(view('store-backend::multi_groupon.index', compact('grouponList')));
        });

    }

    public function create()
    {
        return LaravelAdmin::content(function (Content $content) {

            $content->header('????????????????????????');

            $content->breadcrumb(
                ['text' => '??????????????????', 'url' => 'store/promotion/multiGroupon', 'no-pjax' => 1],
                ['text' => '????????????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

            );

            $content->body(view('store-backend::multi_groupon.create'));
        });
    }

    public function edit($id)
    {
        $groupon = MultiGroupon::find($id);
        $type = request('type');

        return LaravelAdmin::content(function (Content $content) use ($groupon, $type) {

            $content->header('????????????????????????');

            $content->breadcrumb(
                ['text' => '??????????????????', 'url' => 'store/promotion/multiGroupon', 'no-pjax' => 1],
                ['text' => '????????????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

            );

            $content->body(view('store-backend::multi_groupon.edit', compact('groupon', 'type')));
        });
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', 'hour', 'minute']);

        $validator = $this->validationForm();
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->ajaxJson(false, [], 404, $show_warning);
        }

        try {
            DB::beginTransaction();
            $check = $this->specialGoodsService->checkGoodsStatus($data['goods_id']);

            if (!$check) {
                return $this->ajaxJson(false, [], 404, '???????????????????????????????????????');
            }

            $multiGroupon = MultiGroupon::create($data);
            event('promotion.created', [$data['goods_id'], 'multiGroupon', $multiGroupon->id]);
            $this->multiGrouponService->createShareBgImg($multiGroupon);
            DB::commit();

            return $this->ajaxJson(true, [], 0, '');

        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception->getMessage());
            \Log::info($exception->getTraceAsString());
            return $this->ajaxJson(false, [], 404, '????????????');
        }
    }

    protected function validationForm()
    {
        $rules = array(
            'title' => 'required',
            'goods_id' => 'required',
            'starts_at' => 'required | date',
            'ends_at' => 'required | date | after:starts_at',
            'price' => 'required|numeric|min:1',
            'rate' => 'required | integer|min:1',
            'number' => 'required | integer|min:1',
            'sort' => 'required | integer|min:1'
        );
        $message = array(
            "required" => ":attribute ????????????",
            "ends_at.after" => ':attribute ??????????????????????????????',
            "integer" => ':attribute ???????????????',
            "numeric" => ':attribute ???????????????',
            "min" => ':attribute ????????????1'
        );

        $attributes = array(
            "title" => '????????????',
            "starts_at" => '????????????',
            "ends_at" => '??????????????????',
            "price" => '?????????',
            'rate' => '????????????',
            'number' => '????????????',
            'sort' => '??????',
            'goods_id' => '????????????'
        );

        $validator = Validator::make(
            request()->all(),
            $rules,
            $message,
            $attributes
        );

        return $validator;
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'hour', 'minute', 'id']);
        if ($groupon = MultiGroupon::find(request('id'))) {
            $groupon->fill($data);
            $groupon->save();
            return $this->ajaxJson();
        }
        return $this->ajaxJson(false);
    }

    /**
     * ????????????modal
     * @return mixed
     */
    public function getSpuModal()
    {
        return view('store-backend::multi_groupon.includes.modal.getSpu');
    }

    /**
     * ??????????????????
     * @param Request $request
     * @return mixed
     */
    public function getSpuData(Request $request)
    {
        $where = [];
        $where_ = [];

        $where['is_del'] = ['=', 0];
        $where['is_largess'] = ['=', 0];

        if (!empty(request('value')) AND request('field') !== 'sku' AND request('field') !== 'category') {
            $where[request('field')] = ['like', '%' . request('value') . '%'];
        }

        $goods_ids = [];
        if (request('field') == 'sku' && !empty(request('value'))) {
            $goods_ids = $this->goodsService->skuGetGoodsIds(request('value'));
        }
        if (request('field') == 'category' && !empty(request('value'))) {
            $goods_ids = $this->goodsService->categoryGetGoodsIds(request('value'));
        }

        $goods = $this->goodsRepository->getGoodsPaginated($where, $where_, $goods_ids, 10)->toArray();
        $goods = $this->specialGoodsService->filterGoodsStatus($goods);

        return $this->ajaxJson(true, $goods);
    }

    /**
     * ??????/?????????
     * @return mixed
     */
    public function delete()
    {
        if ($groupon = MultiGroupon::find(request('id'))) {
            if (request('type') == 'close') {
                $groupon->status = 0;
            } else {
                $groupon->status = 2;
            }
            $groupon->save();
            event('promotion.deleted', [$groupon->goods_id, 'multiGroupon', $groupon->id]);

            return $this->ajaxJson();
        }
        return $this->ajaxJson(false);
    }

    public function grouponItemList($id)
    {
        $groupon = MultiGroupon::find($id);
        $list = MultiGrouponItems::where('multi_groupon_id', $id)->paginate(8);

        return LaravelAdmin::content(function (Content $content) use ($groupon, $list) {

            $content->header($groupon->title . ' ??????????????????');

            $content->breadcrumb(
                ['text' => '??????????????????', 'url' => 'store/promotion/multiGroupon', 'no-pjax' => 1],
                ['text' => '????????????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

            );

            $content->body(view('store-backend::multi_groupon.item-list', compact('groupon', 'list')));
        });

    }

    /**
     * ????????????modal
     * @return mixed
     */
    public function getRefundModal()
    {
        $groupon_id = request('groupon_id');
        return view('store-backend::multi_groupon.includes.modal.refund', compact('groupon_id'));
    }

    public function getRefundItemsPaginate()
    {
        $page = request('page') ? request('page') : 1;

        $grouponItems = $this->multiGrouponItemRepository->getFailItemsByGrouponIdPaginate(request('groupon_id'));

        $lastPage = $grouponItems->lastPage();

        if (count($grouponItems) > 0) {
            event('multiGroupon.order.fail', [$grouponItems]);
        }

        if ($page == $lastPage) {
            return $this->ajaxJson(true, ['status' => 'done']);
        } else {
            $url_bit = route('admin.promotion.multiGroupon.getRefundItemsPaginate', array_merge(['page' => $page + 1], request()->except('page', 'limit')));
            return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url_bit, 'page' => $page, 'totalPage' => $lastPage]);
        }

    }

    /**
     * ??????????????????
     */
    public function getRefundList()
    {
        $grouponID = request('groupon_id');
        $ids = MultiGrouponUsers::where('multi_groupon_id', $grouponID)->where('status', 1)->get()->pluck('id')->toArray();
        $list = MerchantPay::where('origin_type', 'GROUPON_REFUND')
            ->whereIn('origin_id', $ids)
            ->with('multiGrouponUser')
            ->with('multiGrouponUser.order')
            ->with('multiGrouponUser.user')
            ->paginate(10);

        $items = $this->multiGrouponItemRepository->getFailItemsByGrouponIdPaginate($grouponID, 0);
        $count = 0;  //???????????????????????????
        $refundCount = 0;  //???????????????
        if (count($itemIds = $items->pluck('id')->toArray()) > 0) {
            $count = MultiGrouponUsers::where('multi_groupon_id', $grouponID)
                ->where('status', 1)
                ->whereIn('multi_groupon_items_id', $itemIds)
                ->count();
            $refundCount = MultiGrouponUsers::where('multi_groupon_id', $grouponID)
                ->where('status', 1)
                ->where('refund_status', 1)
                ->whereIn('multi_groupon_items_id', $itemIds)
                ->count();
        }


        return LaravelAdmin::content(function (Content $content) use ($list, $count, $refundCount) {

            $content->header('????????????????????????');

            $content->breadcrumb(
                ['text' => '??????????????????', 'url' => 'store/promotion/multiGroupon', 'no-pjax' => 1],
                ['text' => '????????????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

            );

            $content->body(view('store-backend::multi_groupon.refund-list', compact('list', 'count', 'refundCount')));
        });

    }


}