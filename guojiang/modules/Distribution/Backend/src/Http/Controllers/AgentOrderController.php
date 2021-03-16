<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use GuoJiangClub\Distribution\Backend\Models\AgentOrderItem;
use GuoJiangClub\Distribution\Backend\Repository\OrdersRepository;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Backend\Models\AgentOrder;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentOrderController extends Controller
{
	protected $ordersRepository;

	public function __construct(OrdersRepository $ordersRepository)
	{
		$this->ordersRepository = $ordersRepository;
	}

	public function index()
	{
		$id     = request('id');
		$where  = $this->setCondition();
		$orders = $this->ordersRepository->getAgentOrdersPaginate($where, []);
		$agent  = Agent::find($id);

		return LaravelAdmin::content(function (Content $content) use ($orders, $agent, $id) {

			$content->header('分销订单列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销订单列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent_orders.index', compact('orders', 'agent', 'id')));
		});
	}

	protected function setCondition()
	{
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
			default:
				$status = -1;
		}
		if ($status >= 0) {
			$where['status'] = $status;
		}

		$where['agent_id'] = [request('id')];

		if ($value = request('value') AND $filed = request('field')) {
			if ($filed == 'agent_order_no') {
				$where[$filed] = ['like', '%' . $value . '%'];
			} elseif ($filed == 'order_no') {
				$ids               = Order::where($filed, 'like', '%' . $value . '%')->get()->pluck('id')->toArray();
				$where['order_id'] = $ids;
			} elseif ($filed == 'name') {
				$fromID                 = Agent::where($filed, 'like', '%' . $value . '%')->first()->pluck('id')->toArray();
				$where['from_agent_id'] = $fromID;
			}
		}

		return $where;
	}

	public function show($id)
	{
		$order = AgentOrder::find($id);

		//return view('backend-distribution::agent_orders.show', compact('order'));

		return LaravelAdmin::content(function (Content $content) use ($order) {

			$content->header('查看分销订单详情');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '查看分销订单详情', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent_orders.show', compact('order')));
		});
	}

	/**
	 * for mao da 1
	 *
	 * @param $agent_id
	 *
	 * @return Content
	 */
	public function addAgentOrder($agent_id)
	{
		AgentOrder::where('status', 0)
			->where('settle_days', '>', 1)
			->whereRaw('(DATEDIFF(now(),created_at) >= settle_days)')
			->get();

		$agent = Agent::find($agent_id);

		return LaravelAdmin::content(function (Content $content) use ($agent) {

			$content->header('添加分销佣金');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '添加分销佣金', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent_orders.add', compact('agent')));
		});
	}

	public function postAgentOrder(Request $request)
	{
		try {
			$commission = $request->input('commission');
			$days       = $request->input('days');

			if (!$commission OR !$days) {
				return $this->ajaxJson(false, [], 404, '请填写佣金金额或账期');
			}

			if ($commission < 0) {
				return $this->ajaxJson(false, [], 404, '佣金金额必须大于0');
			}

			if (!is_numeric($days) || strpos($days, ".") !== false) {
				return $this->ajaxJson(false, [], 404, '账期必须大于1，并且是整数');
			}

			DB::beginTransaction();
			$agentOrder = AgentOrder::create([
				'agent_id'         => $request->input('agent_id'),
				'level'            => 1,
				'agent_order_no'   => build_order_no('D'),
				'order_id'         => 0,
				'total_commission' => $commission * 100,
				'commission'       => $commission * 100,
				'status'           => 0,
				'settle_days'      => $days,
			]);

			AgentOrderItem::create([
				'agent_id'         => $request->input('agent_id'),
				'order_item_id'    => 0,
				'agent_order_id'   => $agentOrder->id,
				'rate'             => 100,
				'total_commission' => $commission * 100,
				'commission'       => $commission * 100,
				'status'           => 0,
			]);
			DB::commit();

			return $this->ajaxJson();
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info($exception);

			return $this->ajaxJson(false, [], 404, '保存失败');
		}
	}

}