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

			$content->header('分销数据统计');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销数据统计', 'url' => '', 'no-pjax' => 1]

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
	 * 获取下载日报数据
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

		$title = ['日期', '分销员注册数', '累计分销订单数', '昨日分销订单数', '累计分销订单总金额', '昨日分销订单总金额', '累计结算佣金', '今日结算佣金', '待结算佣金'];

		return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => 'xsl', 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agent_daily_data_']);
		//return view('backend-distribution::statistics', compact('data'));
	}

	/**
	 * 获取下载月报
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

		$title = ['月份', '本月平台总交易额', '平台注册分销员总数', '本月新增注册分销员数', '本月分销订单数', '本月分销订单金额', '本月结算佣金', '待结算佣金'];

		return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => 'xsl', 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agent_month_data_']);
	}

}