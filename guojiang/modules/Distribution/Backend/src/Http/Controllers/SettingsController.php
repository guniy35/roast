<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Distribution\Backend\Models\AgentGoods;
use Illuminate\Http\Request;
use GuoJiangClub\Distribution\Backend\Repository\GoodsRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class SettingsController extends Controller
{
	protected $goodsRepository;

	public function __construct(GoodsRepository $goodsRepository)
	{
		$this->goodsRepository = $goodsRepository;
	}

	public function index()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('分销系统设置');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销设置', 'url' => 'distribution/setting/sys_setting', 'no-pjax' => 1],
				['text' => '分销系统设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '系统设置']

			);

			$content->body(view("backend-distribution::settings.index"));
		});
	}

	public function saveSettings(Request $request)
	{
		$data = ($request->except('_token'));

		if (!$data['distribution_limit']) {
			return $this->ajaxJson(false, [], 404, '提现门槛不能为空');
		}

		foreach ($data['distribution_rate'] as $key => $item) {
			if (!$item['value']) {
				return $this->ajaxJson(false, [], 404, '佣金比例不能为空');
			}
		}

		if ($data['distribution_goods_status'] AND empty($data['distribution_goods_rate'])) {
			return $this->ajaxJson(false, [], 404, '请设置商品默认佣金比例');
		}

		settings()->setSetting($data);

		return $this->ajaxJson();
	}

	public function goods()
	{
		$status               = request('status');
		$criteria['activity'] = $status == 'ACTIVITY' ? 1 : 0;
		$ids                  = [];
		if ($value = request('value')) {
			$where['name'] = ['like', '%' . $value . '%'];
			$ids           = $this->goodsRepository->getGoodsIdsByCriteria($where);
		}

		$goods = $this->goodsRepository->getGoodsPaginate($criteria, $ids);

		return LaravelAdmin::content(function (Content $content) use ($goods, $status) {

			$content->header('分销商品设置');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销设置', 'url' => 'distribution/setting/sys_setting', 'no-pjax' => 1],
				['text' => '分销商品设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '商品设置']

			);

			$content->body(view("backend-distribution::settings.goods", compact('goods', 'status')));
		});
	}

	public function editGoods()
	{
		$goods = AgentGoods::find(request('id'));

		return view("backend-distribution::settings.includes.edit_goods", compact('goods'));
	}

	public function saveGoods()
	{
		$goods             = AgentGoods::find(request('id'));
		$goods->rate       = request('rate');
		$goods->rate_organ = request('rate_organ') ? request('rate_organ') : 0;
		$goods->rate_shop  = request('rate_shop') ? request('rate_shop') : 0;
		$goods->activity   = request('activity');
		$goods->save();

		return $this->ajaxJson();
	}

	public function editBatchGoods()
	{
		if (request('ids') == 'all') {
			$ids = request('ids');
		} else {
			$ids = implode(',', request('ids'));
		}

		$type      = request('type');
		$value     = request('value');
		$status    = request('status');
		$rate_type = request('rate_type');

		return view("backend-distribution::settings.includes.edit_batch_goods", compact('ids', 'type', 'value', 'status', 'rate_type'));
	}

	public function saveBatchGoods()
	{
		$ids      = request('ids');
		$type     = request('type');
		$activity = request('status') == 'ACTIVITY' ? 1 : 0;

		$goods_ids = [];
		if ($value = request('value')) {
			$criteria['name'] = ['like', '%' . $value . '%'];
			$goods_ids        = $this->goodsRepository->getGoodsIdsByCriteria($criteria);
		}

		if ($type == 'status') {
			$data = ['activity' => request('activity')];
		} else {
			$data = [
				'rate'       => request('rate'),
				'rate_organ' => request('rate_organ') ? request('rate_organ') : 0,
				'rate_shop'  => request('rate_shop') ? request('rate_shop') : 0,
			];
			foreach ($data as $key => $value) {
				if (!$value OR $value == 0) {
					unset($data[$key]);
				}
			}
		}

		if ($ids == 'all') {
			if (count($goods_ids) > 0) {
				AgentGoods::whereIn('goods_id', $goods_ids)->where('activity', $activity)->update($data);
			} else {
				AgentGoods::where('activity', $activity)->update($data);
			}
		} else {
			$ids = explode(',', request('ids'));
			AgentGoods::whereIn('id', $ids)->update($data);
		}

		return $this->ajaxJson();
	}

	/**
	 * 同步商品modal
	 *
	 * @return mixed
	 */
	public function syncGoods()
	{
		return view("backend-distribution::settings.includes.sync_goods");
	}

	/**
	 * 同步商品到分销商品表
	 */
	public function postSyncGoods()
	{
		$goods    = Goods::where('is_del', 0)->where('redeem_point', '<=', 0)->paginate(100);
		$lastPage = $goods->lastPage();
		$page     = request('page') ? request('page') : 1;
		$url      = route('admin.distribution.goods.postSyncGoods', ['page' => $page + 1]);

		if ($page > $lastPage) {
			return $this->ajaxJson(true, ['status' => 'complete']);
		}

		foreach ($goods as $item) {
			if (!AgentGoods::where('goods_id', $item->id)->first()) {
				AgentGoods::create([
					'goods_id'   => $item->id,
					'activity'   => request('activity'),
					'rate'       => request('rate') ? request('rate') : 0,
					'rate_organ' => request('rate_organ') ? request('rate_organ') : 0,
					'rate_shop'  => request('rate_shop') ? request('rate_shop') : 0,
				]);
			}
		}

		return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url, 'current_page' => $page, 'total' => $lastPage]);
	}

}