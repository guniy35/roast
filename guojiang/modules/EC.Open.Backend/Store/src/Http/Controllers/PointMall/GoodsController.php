<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers\PointMall;

use GuoJiangClub\EC\Open\Backend\Store\Model\CategoryGroup;
use GuoJiangClub\EC\Open\Backend\Store\Model\Goods;
use GuoJiangClub\EC\Open\Backend\Store\Model\Models;
use GuoJiangClub\EC\Open\Backend\Store\Model\Spec;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ModelsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\SpecRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\AttributeRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\BrandRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\CategoryRepository;
use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ProductRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\GoodsService;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class GoodsController extends Controller
{
	protected $modelsRepository;
	protected $specRepository;
	protected $attributeRepository;
	protected $brandRepository;
	protected $goodsRepository;
	protected $categoryRepository;
	protected $productRepository;
	protected $goodsService;
	protected $cache;

	public function __construct(ModelsRepository $modelsRepository
		, SpecRepository $specRepository
		, AttributeRepository $attributeRepository
		, BrandRepository $brandRepository
		, GoodsRepository $goodsRepository
		, ProductRepository $productRepository
		, CategoryRepository $categoryRepository
		, GoodsService $goodsService
	)
	{

		$this->modelsRepository    = $modelsRepository;
		$this->specRepository      = $specRepository;
		$this->attributeRepository = $attributeRepository;
		$this->brandRepository     = $brandRepository;
		$this->goodsRepository     = $goodsRepository;
		$this->categoryRepository  = $categoryRepository;
		$this->productRepository   = $productRepository;
		$this->goodsService        = $goodsService;
		$this->cache               = cache();
	}

	public function index()
	{
		$where  = [];
		$where_ = [];

		$view                  = !empty(request('view')) ? request('view') : 0;
		$where['is_del']       = ['=', $view];
		$where['is_largess']   = ['=', 1];
		$where['redeem_point'] = ['>', 0];

		if (!empty(request('value')) AND request('field') !== 'sku' AND request('field') !== 'category') {
			$where[request('field')] = ['like', '%' . request('value') . '%'];
		}

		if (!empty(request('store_begin')) && !empty(request('store_end'))) {
			$where['store_nums']  = ['>=', request('store_begin')];
			$where_['store_nums'] = ['<=', request('store_end')];
		}

		if (!empty(request('store_begin'))) {
			$where_['store_nums'] = ['>=', request('store_begin')];
		}

		if (!empty(request('store_end'))) {
			$where_['store_nums'] = ['<=', request('store_end')];
		}

		if (!empty(request('price_begin')) AND !empty(request('price_end')) AND request('price') != 'sku_market_price' AND request('price') != 'sku_sell_price') {
			$where[request('price')]  = ['>=', request('price_begin')];
			$where_[request('price')] = ['<=', request('price_end')];
		}

		if (!empty(request('price_begin')) AND request('price') != 'sku_market_price' AND request('price') != 'sku_sell_price') {
			$where_[request('price')] = ['>=', request('price_begin')];
		}

		if (!empty(request('price_end')) AND request('price') != 'sku_market_price' AND request('price') != 'sku_sell_price') {
			$where_[request('price')] = ['<=', request('price_end')];
		}

//       ????????????
		if (request('lineGoods') == 1) {
			$res = $this->goodsRepository->operationLineGoods('is_del', 0, request('gid'));
//       ????????????
		} elseif (request('lineGoods') == 2) {
			$res = $this->goodsRepository->operationLineGoods('is_del', 2, request('gid'));
		}

		$goods_ids = [];
		if (request('field') == 'sku' && !empty(request('value'))) {
			$goods_ids = $this->goodsService->skuGetGoodsIds(request('value'));
		}
		if (request('field') == 'category' && !empty(request('value'))) {
			$goods_ids = $this->goodsService->categoryGetGoodsIds(request('value'));
		}

		/*???SKU???????????????????????????*/
		if (request('price') == 'sku_market_price' OR request('price') == 'sku_sell_price') {
			$goods_ids_by_sku_price = $this->goodsService->skuPriceGoodsIds(request('price'));
			if (count($goods_ids_by_sku_price)) {
				$goods_ids = array_merge($goods_ids, $goods_ids_by_sku_price);
			}
		}

		$goods = $this->goodsRepository->getGoodsPaginated($where, $where_, $goods_ids);

		if (request('view') == 2) {
			$whereCount           = $where;
			$whereCount['is_del'] = 2;
			$delCount             = $this->goodsRepository->getGoodsPaginated($whereCount, $where_, $goods_ids, 0)->count();
			$whereCount['is_del'] = 0;
			$allCount             = $this->goodsRepository->getGoodsPaginated($whereCount, $where_, $goods_ids, 0)->count();
		} else {
			$whereCount           = $where;
			$whereCount['is_del'] = 0;
			$allCount             = $this->goodsRepository->getGoodsPaginated($whereCount, $where_, $goods_ids, 0)->count();
			$whereCount['is_del'] = 2;
			$delCount             = $this->goodsRepository->getGoodsPaginated($whereCount, $where_, $goods_ids, 0)->count();
		}

		return LaravelAdmin::content(function (Content $content) use ($goods, $allCount, $delCount) {

			$content->header('??????????????????');

			$content->breadcrumb(
				['text' => '??????????????????', 'url' => 'store/point-mall/goods', 'no-pjax' => 1],
				['text' => '??????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

			);

			$content->body(view('store-backend::point_mall.goods.index', compact('goods', 'allCount', 'delCount')));
		});
	}

	public function create()
	{
		$models        = $this->modelsRepository->all();
		$brands        = $this->brandRepository->all();
		$categoryGroup = CategoryGroup::first();

		return LaravelAdmin::content(function (Content $content) use ($models, $brands, $categoryGroup) {

			$content->header('??????????????????');

			$content->breadcrumb(
				['text' => '??????????????????', 'url' => 'store/point-mall/goods', 'no-pjax' => 1],
				['text' => '??????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']

			);

			$content->body(view('store-backend::point_mall.goods.create', compact('models', 'brands', 'categoryGroup')));
		});
	}

	public function edit($id)
	{
		if (!($goods_info = Goods::find($id))) {
			return \Redirect::route('admin.goods.index');
		}

		$redirect_url  = request('redirect_url');
		$cateIds       = $goods_info->categories->pluck('id')->all();
		$cateNames     = $goods_info->categories->all();
		$product       = $goods_info->hasManyProducts()->get();
		$brands        = $this->brandRepository->all();
		$models        = $this->modelsRepository->all();
		$attrArray     = $this->attributeRepository->getAttrDataByModelID($goods_info->model_id);  //????????????ID????????????????????????
		$categories    = $this->categoryRepository->getOneLevelCategory($goods_info->category_group);
		$currAttribute = $this->goodsRepository->getAttrArray($id);     //????????????????????????

		//?????????????????????????????????
		$model    = Models::find($goods_info->model_id);
		$spec     = Spec::whereIn('id', $model->spec_ids)->get();
		$specData = $this->goodsService->handleInitSpecData($spec, $id);

		/*??????????????????*/
		$attrArray = $attrArray->merge($model->hasManyAttribute)->all();

		$point = $goods_info->hasOnePoint()->first();

		return LaravelAdmin::content(function (Content $content) use ($cateNames, $goods_info, $product, $categories, $brands, $models, $attrArray, $currAttribute, $specData, $cateIds, $point, $redirect_url) {

			$content->header('??????????????????');

			$content->breadcrumb(
				['text' => '??????????????????', 'url' => 'store/point-mall/goods', 'no-pjax' => 1],
				['text' => '??????????????????', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '??????????????????']
			);

			$content->body(view('store-backend::point_mall.goods.edit', compact('cateNames', 'goods_info', 'product', 'categories', 'brands', 'models', 'attrArray', 'currAttribute', 'specData', 'cateIds', 'point', 'redirect_url')));
		});
	}
}
