<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use GuoJiangClub\Component\Suit\Models\SuitItems;
use Illuminate\Http\Request;
use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Component\Suit\Repositories\SuitItemRepository;
use GuoJiangClub\Component\Suit\Repositories\SuitRepository;
use GuoJiangClub\Component\Suit\Models\Suit;
use Carbon\Carbon;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\EC\Open\Backend\Store\Model\SpecsValue;
use GuoJiangClub\Component\Product\Repositories\ProductRepository;
use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class SuitController extends Controller
{
	protected $suitRepository;
	protected $suitItemRepository;
	protected $productRepository;
	protected $goodsRepository;

	public function __construct(SuitItemRepository $suitItemRepository
		, SuitRepository $suitRepository
		, ProductRepository $productRepository
		, GoodsRepository $goodsRepository)
	{
		$this->suitRepository     = $suitRepository;
		$this->suitItemRepository = $suitItemRepository;
		$this->productRepository  = $productRepository;
		$this->goodsRepository    = $goodsRepository;;
	}

	public function index()
	{
		$where   = [];
		$orWhere = [];
		$status  = request('status');
		if ($status == 'nstart') {
			$where['status']    = 1;
			$where['starts_at'] = ['>', Carbon::now()];
		}

		if ($status == 'ing') {
			$where['status']    = 1;
			$where['starts_at'] = ['<=', Carbon::now()];
			$where['ends_at']   = ['>', Carbon::now()];
		}

		if ($status == 'end') {
			$where['ends_at']  = ['<', Carbon::now()];
			$orWhere['status'] = 0;
		}

		if (request('title') != '') {
			$where['title'] = ['like', '%' . request('title') . '%'];
		}

		$suits = $this->suitRepository->getSuitList($where, $orWhere);

		return LaravelAdmin::content(function (Content $content) use ($suits) {

			$content->header('套餐管理');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '套餐列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.index', compact('suits')));
		});
	}

	public function toggleSuitStatus(Request $request)
	{
		$status = request('status');
		$id     = request('aid');
		$res    = $this->suitRepository->update(['status' => $status], $id);
		if ($res) {
			return $this->ajaxJson(true, [], 200, '');
		}

		return $this->ajaxJson(false, [], 400, '操作失败');
	}

	public function create()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('新增套餐');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '新增套餐', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.create'));
		});
	}

	public function store()
	{
		$input = request()->except(['_token', 'file']);
		if (!$input['img']) {
			return $this->ajaxJson(false, [], 200, '请上传分享海报');
		}

		if (isset($input['id']) && !empty(isset($input['id']))) {
			if ($this->suitRepository->update($input, $input['id'])) {
				return $this->ajaxJson(true, [], 200, '');
			}
		} else {
			$input['type'] = 1;
			if ($this->suitRepository->create($input)) {
				return $this->ajaxJson(true, [], 200, '');
			}
		}
	}

	public function edit($id)
	{
		$suit = $this->suitRepository->find($id);

		return LaravelAdmin::content(function (Content $content) use ($suit) {

			$content->header('编辑套餐');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '编辑套餐', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.edit', compact('suit')));
		});
	}

	public function destroy()
	{
		$id     = request('id');
		$adItem = $this->suitItemRepository->findByField('suit_id', $id)->first();
		if (is_null($adItem)) {
			$this->suitRepository->delete($id);

			return $this->ajaxJson(true, [], 200, '');
		} else {
			return $this->ajaxJson(false, [], 400, '套餐下非空删除失败');
		}
	}

	//套餐item
	public function createItem($id)
	{
		$name  = request('goods_name');
		$goods = [];
		if (!empty($name)) {
			$goods = Goods::where('name', 'like', '%' . $name . '%')->where('store_nums', '>', 0)->get(['id', 'name', 'sell_price', 'store_nums']);
		}

		return LaravelAdmin::content(function (Content $content) use ($id, $goods) {

			$content->header('添加套餐商品');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '添加套餐商品', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.item.create', compact('id', 'goods')));
		});
	}

	public function editItem($id)
	{
		$suits = SuitItems::with('goods')
			->with('suit')
			->with('product')
			->with('product.goods')
			->find($id);

		return LaravelAdmin::content(function (Content $content) use ($id, $suits) {

			$content->header('修改套餐商品');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '修改套餐商品', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.item.edit', compact('suits', 'id')));
		});
	}

	public function updateItem($id)
	{
		$url   = '';
		$input = request()->except(['_token']);
		if ($this->suitItemRepository->update($input, $id)) {
			$originTotal = $this->suitItemRepository->getOriginTotal($input['suit_id']);
			$total       = $this->suitItemRepository->getTotal($input['suit_id']);
			$this->suitRepository->update(['origin_total' => $originTotal, 'total' => $total], $input['suit_id']);
			$url = route('admin.promotion.suit.ShowItem', ['id' => $input['suit_id']]);

			return $this->ajaxJson(true, ['url' => $url], 200, '');
		}

		return $this->ajaxJson(false, ['url' => $url], 400, '');
	}

	public function storeItem()
	{
		$url   = '';
		$input = request()->except(['_token']);
		if ($this->suitItemRepository->create($input)) {
			$originTotal = $this->suitItemRepository->getOriginTotal($input['suit_id']);
			$total       = $this->suitItemRepository->getTotal($input['suit_id']);
			$this->suitRepository->update(['origin_total' => $originTotal, 'total' => $total], $input['suit_id']);
			$url = route('admin.promotion.suit.ShowItem', ['id' => $input['suit_id']]);

			return $this->ajaxJson(true, ['url' => $url], 200, '');
		}

		return $this->ajaxJson(false, ['url' => $url], 400, '');
	}

	public function ShowItem($id)
	{
		$suits = [];
		$data  = Suit::with('items.goods')
			->with('items.product')
			->with(['items' => function ($query) {
				$query->orderBy('sort', 'desc');
			}])
			->whereHas('items', function ($query) use ($id) {
				return $query->where('suit_id', $id);
			})->first();

		if (isset($data->items) && count($data->items) > 0) {
			$suits = $data->items;
		}

		$title = $this->suitRepository->find($id)->title;

		return LaravelAdmin::content(function (Content $content) use ($id, $suits, $title) {

			$content->header('查看套餐商品');

			$content->breadcrumb(
				['text' => '套餐管理', 'url' => 'store/promotion/suit', 'no-pjax' => 1],
				['text' => '查看套餐商品', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '套餐管理']

			);

			$content->body(view('store-backend::suit.item.index', compact('id', 'suits', 'title')));
		});
	}

	public function getGoodsInfo()
	{
		if (!empty(request('sku'))) {
			$sku = request('sku');
			if ($res = $this->productRepository->with('goods')->findWhere(['sku' => $sku])->first()) {
				$specs_value = '';
				if (isset($res->specID) && !empty($res->specID)) {
					$specID = $res->specID;
					foreach ($specID as $item) {
						$str         = SpecsValue::find(['id' => intval($item)])->first()->name . " ";
						$specs_value .= $str;
					}
				}
				$res->specs_value_name = $specs_value;

				return $this->ajaxJson(true, $res, 200, '');
			}

			return $this->ajaxJson(false, [], 400, '');
		}

		if (!empty(request('spu'))) {
			$spu   = request('spu');
			$where = [];
			if (is_numeric($spu)) {
				$where['id'] = $spu;
			} else {
				$where['name'] = $spu;
			}

			if ($res = $this->goodsRepository->findWhere($where)->first()) {
				return $this->ajaxJson(true, $res, 200, '');
			}

			return $this->ajaxJson(false, [], 400, '');
		}
	}

	public function toggleSuitItemStatus()
	{
		$status = request('status');
		$id     = request('aid');
		$item   = $this->suitItemRepository->findWhere(['id' => $id])->first();
		if ($item) {
			$user         = SuitItems::find($id);
			$user->status = $status;
			$user->save();
			$originTotal = $this->suitItemRepository->getOriginTotal($item->suit_id);
			$total       = $this->suitItemRepository->getTotal($item->suit_id);
			$this->suitRepository->update(['origin_total' => $originTotal, 'total' => $total], $item->suit_id);

			return $this->ajaxJson(true, [], 200, '');
		}

		return $this->ajaxJson(false, [], 400, '操作失败');
	}

	public function destroyItem()
	{
		$id   = request('id');
		$item = $this->suitItemRepository->findWhere(['id' => $id])->first();
		if ($item) {
			$this->suitItemRepository->delete($id);
			$originTotal = $this->suitItemRepository->getOriginTotal($item->suit_id);
			$total       = $this->suitItemRepository->getTotal($item->suit_id);
			$this->suitRepository->update(['origin_total' => $originTotal, 'total' => $total], $item->suit_id);

			return $this->ajaxJson(true, [], 200, '');
		}

		return $this->ajaxJson(false, [], 400, '删除失败');
	}

}