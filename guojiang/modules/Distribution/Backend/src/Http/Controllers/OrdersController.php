<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Backend\Models\AgentOrder;
use GuoJiangClub\Distribution\Backend\Repository\OrdersRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class OrdersController extends Controller
{
	protected $orderRepository;
	protected $cache;

	public function __construct(OrdersRepository $ordersRepository)
	{
		$this->orderRepository = $ordersRepository;
		$this->cache           = cache();
	}

	public function index()
	{
		$condition = $this->setCondition();
		$where     = $condition[0];
		$time      = $condition[1];
		$orders    = $this->orderRepository->getAgentOrdersPaginate($where, $time, 20);

		return LaravelAdmin::content(function (Content $content) use ($orders) {

			$content->header('分销订单列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销订单列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '订单列表']

			);

			$content->body(view('backend-distribution::orders.index', compact('orders')));
		});
	}

	protected function setCondition()
	{
		$status = 0;
		switch (request('status')) {
			case 'STATUS_UNSETTLED':
				$status = 0;
				break;
			case 'STATUS_STATE':
				$status = 1;
				break;
			case 'STATUS_INVALID':
				$status = 2;
				break;
		}

		$where['status'] = $status;
		//$where['level'] = 1;

		if (request('status') == 'ALL') {
			unset($where['status']);
		}

		if ($value = request('value') AND $filed = request('field')) {
			if ($filed == 'agent_order_no') {
				$where[$filed] = ['like', '%' . $value . '%'];
			} elseif ($filed == 'order_no') {
				$ids               = Order::where($filed, 'like', '%' . $value . '%')->get()->pluck('id')->toArray();
				$where['order_id'] = $ids;
			} elseif ($filed == 'name') {
				$ids = Agent::where($filed, 'like', '%' . $value . '%')->get()->pluck('id')->toArray();

				$where['agent_id'] = $ids;
			}
		}

		$time = [];
		/*申请时间*/
		if (!empty(request('stime')) && !empty(request('etime'))) {
			$time['created_at'] = [request('stime'), request('etime')];
		} elseif (!empty(request('stime'))) {
			$time['created_at'] = [request('stime'), Carbon::now()];
		} elseif (!empty(request('etime'))) {
			$time['created_at'] = ['1970-01-01 00:00:00', request('etime')];
		}

		return [$where, $time];
	}

	/**
	 * 获取需要导出的数据
	 */
	public function getExportData()
	{
		$page  = request('page') ? request('page') : 1;
		$limit = request('limit') ? request('limit') : 20;
		$type  = request('type');

		$condition = $this->setCondition();
		$where     = $condition[0];
		$time      = $condition[1];

		$orders = $this->orderRepository->getAgentOrdersPaginate($where, $time, $limit);

		$lastPage = $orders->lastPage();

		$agentExcelData = $this->orderRepository->formatToExcelData($orders);

		if ($page == 1) {
			session(['export_agent_order_cache' => generate_export_cache_name('export_agent_order_cache_')]);
		}
		$cacheName = session('export_agent_order_cache');

		if ($this->cache->has($cacheName)) {
			$cacheData = $this->cache->get($cacheName);
			$this->cache->put($cacheName, array_merge($cacheData, $agentExcelData), 300);
		} else {
			$this->cache->put($cacheName, $agentExcelData, 300);
		}

		if ($page == $lastPage) {
			$title = ['分销单号', '商城单号', '订单状态', '订单创建时间', '订单支付时间', '来源分销员姓名', '来源分销员手机号', '佣金(元)', '佣金结算状态', '佣金结算时间', '是否售后', '商品信息', '现金支付', '余额支付'];

			return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => $type, 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agent_order_data_']);
		} else {
			$url_bit = route('admin.distribution.orders.getExportData', array_merge(['page' => $page + 1, 'limit' => $limit], request()->except('page', 'limit')));

			return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url_bit, 'page' => $page, 'totalPage' => $lastPage]);
		}
	}

	public function show($id)
	{
		$order = AgentOrder::find($id);

		//return view('backend-distribution::orders.show', compact('order'));

		return LaravelAdmin::content(function (Content $content) use ($order) {

			$content->header('查看分销订单详情');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '订单列表', 'url' => 'distribution/orders?status=ALL', 'no-pjax' => 1],
				['text' => '查看分销订单详情', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '订单列表']

			);

			$content->body(view('backend-distribution::orders.show', compact('order')));
		});
	}

}