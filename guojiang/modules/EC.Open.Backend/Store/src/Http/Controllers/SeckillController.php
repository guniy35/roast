<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Distribution\Core\Models\AgentGoods;
use GuoJiangClub\EC\Open\Backend\Store\Model\Seckill;
use GuoJiangClub\EC\Open\Backend\Store\Model\SeckillItem;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\SeckillRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\GoodsService;
use GuoJiangClub\EC\Open\Backend\Store\Service\SpecialGoodsService;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Validator;
use DB;

class SeckillController extends Controller
{

	protected $seckillRepository;
	protected $goodsService;
	protected $goodsRepository;
	protected $specialGoodsService;

	public function __construct(SeckillRepository $seckillRepository
		, GoodsService $goodsService
		, GoodsRepository $goodsRepository
		, SpecialGoodsService $specialGoodsService
	)
	{
		$this->seckillRepository   = $seckillRepository;
		$this->goodsService        = $goodsService;
		$this->goodsRepository     = $goodsRepository;
		$this->specialGoodsService = $specialGoodsService;
	}

	public function index()
	{
		$seckill = $this->seckillRepository->getSeckillPaginated();

		return LaravelAdmin::content(function (Content $content) use ($seckill) {

			$content->header('秒杀活动列表');

			$content->breadcrumb(
				['text' => '秒杀管理', 'url' => 'store/promotion/seckill', 'no-pjax' => 1],
				['text' => '秒杀活动列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '秒杀管理']

			);

			$content->body(view('store-backend::seckill.index', compact('seckill')));
		});
	}

	public function create()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('新建秒杀活动');

			$content->breadcrumb(
				['text' => '秒杀管理', 'url' => 'store/promotion/seckill', 'no-pjax' => 1],
				['text' => '新建秒杀活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '秒杀管理']

			);

			$content->body(view('store-backend::seckill.create'));
		});
	}

	public function edit($id)
	{
		$seckill = $this->seckillRepository->find($id);
		$items   = $seckill->items->sortByDesc('sort')->values();
		$num     = count($items);
		$ids     = implode(',', $items->pluck('item_id')->toArray());

		return LaravelAdmin::content(function (Content $content) use ($seckill, $num, $ids, $items) {

			$content->header('修改秒杀活动');

			$content->breadcrumb(
				['text' => '秒杀管理', 'url' => 'store/promotion/seckill', 'no-pjax' => 1],
				['text' => '修改秒杀活动', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '秒杀管理']

			);

			$content->body(view('store-backend::seckill.edit', compact('seckill', 'num', 'ids', 'items')));
		});
	}

	public function getSpu()
	{
		return view('store-backend::seckill.includes.modal.getSpu');
	}

	public function getSpuData(Request $request)
	{
		$id  = $request->input('id') ? $request->input('id') : 0;
		$ids = [];
		if ($request->input('ids')) {
			$ids = explode(',', $request->input('ids'));
		}

		$where  = [];
		$where_ = [];

		$where['is_del']     = ['=', 0];
		$where['is_largess'] = ['=', 0];

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

		if (!empty(request('price_begin')) && !empty(request('price_end'))) {
			$where[request('price')]  = ['>=', request('price_begin')];
			$where_[request('price')] = ['<=', request('price_end')];
		}

		if (!empty(request('price_begin'))) {
			$where_[request('price')] = ['>=', request('price_begin')];
		}

		if (!empty(request('price_end'))) {
			$where_[request('price')] = ['<=', request('price_end')];
		}

		$goods_ids = [];
		if (request('field') == 'sku' && !empty(request('value'))) {
			$goods_ids = $this->goodsService->skuGetGoodsIds(request('value'));
		}
		if (request('field') == 'category' && !empty(request('value'))) {
			$goods_ids = $this->goodsService->categoryGetGoodsIds(request('value'));
		}

		$goods = $this->goodsRepository->getGoodsPaginated($where, $where_, $goods_ids, 10)->toArray();

		$goods        = $this->specialGoodsService->filterGoodsStatus($goods, 'seckill', $id);
		$goods['ids'] = $ids;

		return $this->ajaxJson(true, $goods);
	}

	/**
	 * 展示选择的商品
	 */
	public function getSelectGoods()
	{
		$num      = request('num') + 1;
		$ids      = explode(',', request('ids'));
		$selected = explode(',', request('select'));
		$goods_id = array_merge(array_diff($ids, $selected), array_diff($selected, $ids));
		$goods    = $this->goodsRepository->getGoodsPaginated([], [], $goods_id, 0);

		foreach ($goods as $item) {
			$item->rate = 0;
			if ($agentGoods = AgentGoods::where('goods_id', $item->id)->first()) {
				$item->rate = $agentGoods->rate;
			}
		}

		return view('store-backend::seckill.includes.select_goods', compact('goods', 'num'));
	}

	/**
	 * 创建活动
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function store(Request $request)
	{
		$data = $request->except(['_token', 'hour', 'minute', 'goods_name', 'upload_image']);

		$validator = $this->validationForm();
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return $this->ajaxJson(false, [], 404, $show_warning);
		}

		try {
			DB::beginTransaction();

			foreach ($data['item'] as $key => $item) {
				$check = $this->specialGoodsService->checkGoodsStatus($item['item_id']);
				if (!$check) {
					return $this->ajaxJson(false, [], 404, $request->only('goods_name')['goods_name'][$key] . ' 已经参与其他有效活动');
				}
			}

			$seckill = Seckill::create($data['base']);
			if ($seckill) {
				$seckill->items()->createMany($data['item']);

				$goodsIds = array_column($data['item'], 'item_id');
				event('promotion.created', [$goodsIds, 'seckill', $seckill->id]);
			}

			DB::commit();

			return $this->ajaxJson(true, [], 0, '');
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception);

			return $this->ajaxJson(false, [], 404, '保存失败');
		}
	}

	protected function validationForm()
	{
		$rules   = [
			'base.title'           => 'required',
			'base.auto_close'      => 'required | integer | min:0',
			'base.starts_at'       => 'required | date',
			'base.ends_at'         => 'required | date | after:base.starts_at',
			'item'                 => 'required',
			'item.*.seckill_price' => 'required|numeric|min:1',
			'item.*.limit'         => 'required | integer|min:1',
			'item.*.rate'          => 'required | integer|min:0',
			'item.*.sell_num'      => 'required | integer|min:0',
			'item.*.sort'          => 'required | integer|min:1',
		];
		$message = [
			"required"                 => ":attribute 不能为空",
			"base.ends_at.after"       => ':attribute 不能早于活动开始时间',
			"integer"                  => ':attribute 必须是整数',
			"base.auto_close.min"      => ':attribute 不能为负数',
			"numeric"                  => ':attribute 必须是数值',
			"item.*.seckill_price.min" => ':attribute 不能小于1元',
			"item.*.limit.min"         => ':attribute 不能小于1件',
			"item.*.rate.min"          => ':attribute 不能为负数',
			'item.*.sell_num.min'      => ':attribute 不能为负数',
			'item.*.sort.min'          => ':attribute 不能小于1',
		];

		$attributes = [
			"base.title"           => '活动名称',
			"base.auto_close"      => '订单自动关闭时间',
			"base.starts_at"       => '开始时间',
			"base.ends_at"         => '领取截止时间',
			"item"                 => '活动商品',
			'item.*.seckill_price' => '秒杀价格',
			'item.*.limit'         => '限购数量',
			'item.*.rate'          => '佣金比例',
			'item.*.sell_num'      => '销量展示',
			'item.*.sort'          => '排序',
		];

		$validator = Validator::make(
			request()->all(),
			$rules,
			$message,
			$attributes
		);

		return $validator;
	}

	/**
	 * 更新已开始的活动
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function updateDisable(Request $request)
	{
		$data = $request->only(['id', 'item']);

		foreach ($data['item'] as $key => $item) {

			if ($item['status'] == 1) {
				$check = $this->specialGoodsService->checkGoodsStatus($item['item_id'], 'seckill', request('id'));
				if (!$check) {
					return $this->ajaxJson(false, [], 404, $request->only('goods_name')['goods_name'][$key] . ' 已经参与其他有效活动');
				}
			}

			if ($item['sell_num'] < 0 OR !is_numeric($item['sell_num'])) {
				return $this->ajaxJson(false, [], 404, '销量展示 输入有误');
			}

			if ($item['sort'] < 0 OR !is_numeric($item['sort'])) {
				return $this->ajaxJson(false, [], 404, '排序 输入有误');
			}
		}

		try {
			DB::beginTransaction();
			foreach ($data['item'] as $item) {
				$seckillItem            = SeckillItem::find($item['id']);
				$seckillItem->status    = $item['status'];
				$seckillItem->sort      = $item['sort'];
				$seckillItem->recommend = $item['recommend'];
				if ($item['sell_num'] > 0) {
					$seckillItem->sell_num = $item['sell_num'];
				}

				$seckillItem->save();

				if ($item['status'] == 0) {
					event('promotion.deleted', [$item['item_id'], 'seckill', $seckillItem->seckill_id]);
				}
			}
			DB::commit();

			return $this->ajaxJson(true, [], 0, '');
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception);

			return $this->ajaxJson(false, [], 404, '保存失败');
		}
	}

	/**
	 * 更新未开始活动
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function update(Request $request)
	{
		$data = $request->except(['_token', 'hour', 'minute', 'goods_name', 'upload_image']);
		// dd($data);
		$validator = $this->validationForm();
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return $this->ajaxJson(false, [], 404, $show_warning);
		}

		try {
			DB::beginTransaction();

			foreach ($data['item'] as $key => $item) {
				if ($item['status'] == 1) {
					$check = $this->specialGoodsService->checkGoodsStatus($item['item_id'], 'seckill', $data['id']);
					if (!$check) {
						return $this->ajaxJson(false, [], 404, $request->only('goods_name')['goods_name'][$key] . ' 已经参与其他有效活动');
					}
				}
			}

			$seckill = $this->seckillRepository->update($data['base'], $data['id']);

			if ($seckill) {
				$handleData = $this->seckillRepository->handleUpdateItem($data['item']);

				if (count($handleData['createData']) > 0) {
					$seckill->items()->createMany($handleData['createData']);

					$goodsIds = array_column($handleData['createData'], 'item_id');
					event('promotion.created', [$goodsIds, 'seckill', $seckill->id]);
				}
				foreach ($handleData['updateData'] as $item) {
					$seckillItem = SeckillItem::find($item['id']);
					$seckillItem->fill($item);
					$seckillItem->save();

					if ($item['status'] == 0) {
						event('promotion.deleted', [$item['item_id'], 'seckill', $seckill->id]);
					}
				}
			}

			if ($data['delete_item']) {
				$deleteIds = explode(',', $data['delete_item']);
				foreach ($deleteIds as $id) {
					$item = SeckillItem::find($id);
					event('promotion.deleted', [$item['item_id'], 'seckill', $item->seckill_id]);
				}
				SeckillItem::destroy($deleteIds);
			}

			DB::commit();

			return $this->ajaxJson(true, [], 0, '');
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception);

			return $this->ajaxJson(false, [], 404, '保存失败');
		}
	}

	/**
	 * 删除,失效秒杀活动
	 *
	 * @param $id
	 */
	public function delete($id)
	{
		$seckill = $this->seckillRepository->find($id);
		if ($seckill->check_status == 1) {
			return $this->ajaxJson(false, [], 404, '活动已开始，不能删除');
		}

		if (request('type') == 'close') { //使失效
			$seckill->status = 0;
		} else {
			$seckill->status = 2;
		}

		$seckill->save();

		foreach ($seckill->items as $item) {
			event('promotion.deleted', [$item->item_id, 'seckill', $id]);
		}
		$seckill->items()->delete();

		return $this->ajaxJson();
	}

}