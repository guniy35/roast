<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Backend\Models\AgentCash;
use GuoJiangClub\Distribution\Backend\Models\AgentCommission;
use GuoJiangClub\Distribution\Backend\Models\AgentOrder;
use GuoJiangClub\Distribution\Backend\Models\AgentUserRelation;
use GuoJiangClub\Distribution\Backend\Services\DataStatisticsService;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class DashboardController extends Controller
{

	protected $dataService;
	protected $cache;

	public function __construct(DataStatisticsService $dataStatisticsService)
	{
		$this->dataService = $dataStatisticsService;
		$this->cache       = cache();
	}

	/**
	 * @return \Illuminate\View\View
	 */
	public function index()
	{

		$agentCount = Agent::where('created_at', '>', Carbon::now()->format('Y-m-d'))->count();
		$totalAgent = Agent::where('status', 1)->count();

		$order      = AgentOrder::where('created_at', '>', Carbon::now()->format('Y-m-d'))
			->where('level', 1)->get();
		$totalOrder = AgentOrder::where('level', 1)->count();

		$orderCount      = $order->count();
		$commissionCount = $order->sum('total_commission');

//        $cashCount = AgentCash::where('settle_time', '>', Carbon::now()->format('Y-m-d'))
//            ->where('status', 2)->get()->sum('amount');
//        $totalCash = AgentCash::where('status', 2)->get()->sum('amount');

		$cashCount = AgentCommission::where('created_at', '>', Carbon::now()->format('Y-m-d'))
			->get()->sum('commission');
		$totalCash = AgentCommission::all()->sum('commission');

		$userCount = AgentUserRelation::where('created_at', '>', Carbon::now()->format('Y-m-d'))->count();
		$totalUser = AgentUserRelation::count();

		/*top 5*/
		$agents = Agent::where('status', 1)->withCount('orders')->orderBy('orders_count', 'desc')->take(5)->get();

		//return view('backend-distribution::dashboard', compact('agentCount', 'orderCount',
		// 'commissionCount', 'cashCount', 'agents', 'userCount', 'totalAgent', 'totalOrder',
		// 'totalCash', 'totalUser'));

		return LaravelAdmin::content(function (Content $content) use (
			$agentCount, $orderCount,
			$commissionCount, $cashCount, $agents, $userCount, $totalAgent, $totalOrder,
			$totalCash, $totalUser
		) {

			$content->header('??????????????????');

			$content->breadcrumb(
				['text' => '????????????', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '??????????????????', 'url' => '', 'no-pjax' => 1]

			);

			$content->body(view('backend-distribution::dashboard', compact('agentCount', 'orderCount',
				'commissionCount', 'cashCount', 'agents', 'userCount', 'totalAgent', 'totalOrder',
				'totalCash', 'totalUser')));
		});
	}

	public function getDashBoardData()
	{
		$date = [
			Carbon::now()->addDay(-7)->format('Y-m-d'),
			Carbon::now()->addDay(-6)->format('Y-m-d'),
			Carbon::now()->addDay(-5)->format('Y-m-d'),
			Carbon::now()->addDay(-4)->format('Y-m-d'),
			Carbon::now()->addDay(-3)->format('Y-m-d'),
			Carbon::now()->addDay(-2)->format('Y-m-d'),
			Carbon::now()->addDay(-1)->format('Y-m-d'),
		];

		$time = array_merge($date, [Carbon::now()->format('Y-m-d')]);

		$agentCount = [];
		$orderCount = [];
		$userCount  = [];
		$cashCount  = [];
		for ($i = 0; $i < 7; $i++) {
			$agent = Agent::where('created_at', '>=', $time[$i])->where('created_at', '<', $time[$i + 1])->count();
			array_push($agentCount, $agent);

			$order = AgentOrder::where('created_at', '>=', $time[$i])->where('created_at', '<', $time[$i + 1])
				->where('level', 1)->count();
			array_push($orderCount, $order);

			$user = AgentUserRelation::where('created_at', '>=', $time[$i])->where('created_at', '<', $time[$i + 1])->count();
			array_push($userCount, $user);

			$cash = AgentCash::where('settle_time', '>=', $time[$i])->where('settle_time', '<', $time[$i + 1])
				->where('status', 2)->get()->sum('amount');
			array_push($cashCount, $cash);
		}

		$data = [
			'date'       => $date,
			'agentCount' => $agentCount,
			'orderCount' => $orderCount,
			'userCount'  => $userCount,
			'cashCount'  => $cashCount,
		];

		return $this->ajaxJson(true, $data);
	}

	/**
	 * ????????????????????????
	 *
	 * @return mixed
	 */
	public function dataStatistics()
	{
		$time = $this->dataService->getDate();
		$data = $this->dataService->getDayData($time);

		session(['export_daily_data_cache' => generate_export_cache_name('export_daily_data_cache_')]);
		$cacheName = session('export_daily_data_cache');

		$this->cache->put($cacheName, [$data], 300);

		$title = ['??????', '??????????????????', '?????????????????????', '?????????????????????', '???????????????????????????', '???????????????????????????', '??????????????????', '??????????????????', '???????????????'];

		return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => 'xsl', 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agent_daily_data_']);
		//return view('backend-distribution::statistics', compact('data'));
	}

	/**
	 * ??????????????????
	 *
	 * @return mixed
	 */
	public function monthDataStatistics()
	{
		$time = $this->dataService->getDate(true);
		$data = $this->dataService->getMonthData($time);

		session(['export_month_data_cache' => generate_export_cache_name('export_month_data_cache_')]);
		$cacheName = session('export_month_data_cache');

		$this->cache->put($cacheName, [$data], 300);

		$title = ['??????', '????????????????????????', '???????????????????????????', '??????????????????????????????', '?????????????????????', '????????????????????????', '??????????????????', '???????????????'];

		return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => 'xsl', 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agent_month_data_']);
	}

}