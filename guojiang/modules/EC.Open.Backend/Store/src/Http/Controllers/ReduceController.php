<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\Component\Reduce\Models\Reduce;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ReduceRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ReduceItemsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ReduceUsersRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\GoodsService;
use GuoJiangClub\EC\Open\Backend\Store\Service\SpecialGoodsService;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Validator;
use DB;


class ReduceController extends Controller
{
    protected $reduceRepository;
    protected $reduceItemsRepository;
    protected $reduceUsersRepository;
    protected $goodsService;
    protected $goodsRepository;


    public function __construct(ReduceRepository $reduceRepository,
                                ReduceItemsRepository $reduceItemRepository,
                                ReduceUsersRepository $reduceUsersRepository,
                                GoodsService $goodsService,
                                SpecialGoodsService $specialGoodsService,
                                GoodsRepository $goodsRepository
    )
    {
        $this->reduceRepository = $reduceRepository;
        $this->reduceItemsRepository = $reduceItemRepository;
        $this->reduceUsersRepository = $reduceUsersRepository;
        $this->goodsService = $goodsService;
        $this->goodsRepository = $goodsRepository;
        $this->specialGoodsService = $specialGoodsService;


    }

    public function index()
    {

        $Lists = $this->reduceRepository->getReduceRepositoryPaginated();

        return LaravelAdmin::content(function (Content $content) use ($Lists) {

            $content->header('砍价活动列表');

            $content->breadcrumb(
                ['text' => '砍价管理', 'url' => 'store/promotion/reduce', 'no-pjax' => 1],
                ['text' => '砍价活动列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '砍价管理']

            );

            $content->body(view('store-backend::reduce.index', compact('Lists')));
        });

    }


    public function delete()
    {
        if ($reduce = Reduce::find(request('id'))) {
            if (request('type') == 'close') {
                $reduce->status = 0;
            } else {
                $reduce->status = 2;
                $reduce->store_nums = 0;
                $reduce->reduce_store_nums = 0;
            }
            $reduce->save();

            return $this->ajaxJson();
        }
        return $this->ajaxJson(false);
    }


    public function create()
    {
        return LaravelAdmin::content(function (Content $content) {

            $content->header('创建砍价活动');

            $content->breadcrumb(
                ['text' => '砍价管理', 'url' => 'store/promotion/reduce', 'no-pjax' => 1],
                ['text' => '创建砍价活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '砍价管理']

            );

            $content->body(view('store-backend::reduce.create'));
        });
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', 'minute']);

        $validator = $this->validationForm();
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->ajaxJson(false, [], 404, $show_warning);
        }

        unset($data['goods_nums']); unset($data['sell_price']);
        $data['store_nums'] = $data['reduce_store_nums'];
        $reduce = Reduce::create($data);
        if ($reduce) {
            return $this->ajaxJson(true, [], 0, '');
        }
        return $this->ajaxJson(false, [], 400, '创建失败');

    }


    public function edit($id)
    {
        $reduce = Reduce::find($id);
        $type = request('type');

        return LaravelAdmin::content(function (Content $content) use ($reduce, $type) {

            $content->header('编辑砍价活动');

            $content->breadcrumb(
                ['text' => '砍价管理', 'url' => 'store/promotion/reduce', 'no-pjax' => 1],
                ['text' => '编辑砍价活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '砍价管理']

            );

            $content->body(view('store-backend::reduce.edit', compact('reduce', 'type')));
        });
    }


    protected function validationForm()
    {

        $price_=intval(request('number'))*0.01;

        $rules = array(
            'title' => 'required',
            'goods_id' => 'required',
            'starts_at' => 'required | date',
            'ends_at' => 'required | date | after:starts_at',
            'price' => 'required|numeric|min:0|max:' . sprintf('%01.2f',request('sell_price')-$price_, 2),
            'hour' => 'required | integer|min:1|max:24',
            'number' => 'required | integer|min:2|max:100',
            'sort' => 'required | integer|min:1',
            'reduce_store_nums' => 'required | integer|min:0|max:' . request('goods_nums'),
            'goods_nums' => 'required | integer|min:1',
        );
        $message = array(
            "required" => ":attribute 不能为空",
            "ends_at.after" => ':attribute 不能早于活动开始时间',
            "integer" => ':attribute 必须是整数',
            "numeric" => ':attribute 必须是数值',
            "min" => ':attribute 不能小于:min',
            "max" => ':attribute 不能大于:max'
        );

        $attributes = array(
            "title" => '活动名称',
            "starts_at" => '开始时间',
            "ends_at" => '领取截止时间',
            "price" => '砍价底价',
            'hour' => '砍价有效期',
            'number' => '帮砍人数',
            'sort' => '排序',
            'goods_id' => '商品选择',
            'reduce_store_nums' => '砍价商品库存',
            'goods_nums' => '商品库存'
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
        $data = $request->except(['_token', 'minute', 'id']);

        $validator = $this->validationForm();
        if ($validator->fails()) {
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return $this->ajaxJson(false, [], 404, $show_warning);
        }

        if ($reduce = Reduce::find(request('id'))) {
            unset($data['goods_nums']);
            $reduce->fill($data);
            $order_goods_nums = $reduce->reduce_store_nums - $reduce->store_nums;
            $reduce->reduce_store_nums = $data['reduce_store_nums'] + $order_goods_nums;
            $reduce->store_nums = $data['reduce_store_nums'];
            $reduce->save();

            return $this->ajaxJson();
        }
        return $this->ajaxJson(false);
    }

    /**
     * 选择商品modal
     * @return mixed
     */
    public function getSpuModal()
    {
        return view('store-backend::reduce.includes.modal.getSpu');
    }

    /**
     * 获取商品数据
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


    public function getItemLists()
    {

        $limit = request('limit') ? request('limit') : 10;

        $Lists = $this->reduceItemsRepository->getReduceItemsPaginated($limit);

        return LaravelAdmin::content(function (Content $content) use ($Lists) {

            $content->header('用户砍价活动列表');

            $content->description('(' . request('title') . ')');


            $content->breadcrumb(
                ['text' => '砍价管理', 'url' => 'store/promotion/reduce', 'no-pjax' => 1],
                ['text' => '用户砍价活动列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '砍价管理']

            );

            $content->body(view('store-backend::reduce.item', compact('Lists')));
        });

    }


    public function getUserLists()
    {

        $limit = request('limit') ? request('limit') : 10;

        $reduceItem = $this->reduceItemsRepository->with('reduce')->find(request('reduce_items_id'));

        $Lists = $this->reduceUsersRepository->getReduceUsersPaginated($limit);

        return LaravelAdmin::content(function (Content $content) use ($Lists, $reduceItem) {

            $content->header('帮砍用户记录列表');

            $content->breadcrumb(
                ['text' => '砍价管理', 'url' => 'store/promotion/reduce', 'no-pjax' => 1],
                ['text' => '帮砍用户记录列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '砍价管理']

            );

            $content->body(view('store-backend::reduce.user', compact('Lists', 'reduceItem')));
        });
    }


    public function getHelpTextModal()
    {
        return view('store-backend::reduce.includes.modal.help-text');
    }


    public function settings()
    {

        $data = request()->except('_token', 'file');

        settings()->setSetting($data);

        $this->ajaxJson();
    }

}