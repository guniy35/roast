<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Backend\Models\BalanceCash;
use GuoJiangClub\Distribution\Backend\Repository\CashRepository;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class CashController extends Controller
{
	protected $cashRepository;
	protected $cache;
	protected $paymentService;

	//, PaymentService $paymentService
	public function __construct(CashRepository $cashRepository)
	{
		$this->cashRepository = $cashRepository;
		//$this->paymentService = $paymentService;
		$this->cache          = cache();
	}

	public function index()
	{
		$condition = $this->setCondition();
		$where     = $condition[0];
		$time      = $condition[1];
		$cash      = $this->cashRepository->getCashPaginate($where, $time, 15);

		return LaravelAdmin::content(function (Content $content) use ($cash) {

			$content->header('佣金提现申请列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '佣金提现管理', 'url' => 'distribution/cash?status=STATUS_AUDIT', 'no-pjax' => 1],
				['text' => '佣金提现申请列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '佣金提现管理']

			);

			$content->body(view('backend-distribution::cash.index', compact('cash')));
		});
	}

	protected function setCondition()
	{
		$status = request('status');
		if (!$status OR $status == 'STATUS_AUDIT') {
			$status = 0;
		} elseif ($status == 'STATUS_WAIT_PAY') {
			$status = 1;
		} elseif ($status == 'STATUS_PAY') {
			$status = 2;
		} else {
			$status = 3;
		}
		$where['status'] = $status;

		if ($value = request('value')) {
			$where['filter'] = ['like', '%' . $value . '%'];
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

		$cash = $this->cashRepository->getCashPaginate($where, $time, $limit);

		$lastPage = $cash->lastPage();

		$agentExcelData = $this->cashRepository->formatToExcelData($cash);

		if ($page == 1) {
			session(['export_cash_cache' => generate_export_cache_name('export_cash_cache_')]);
		}
		$cacheName = session('export_cash_cache');

		if ($this->cache->has($cacheName)) {
			$cacheData = $this->cache->get($cacheName);
			$this->cache->put($cacheName, array_merge($cacheData, $agentExcelData), 300);
		} else {
			$this->cache->put($cacheName, $agentExcelData, 300);
		}

		if ($page == $lastPage) {
			$title = ['姓名', '手机', '申请时间', '工单号', '提现金额(元)', '提现方式', '账号', '账户姓名', '状态'];

			return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => $type, 'title' => $title, 'cache' => $cacheName, 'prefix' => 'cash_data_']);
		} else {
			$url_bit = route('admin.balance.cash.getExportData', array_merge(['page' => $page + 1, 'limit' => $limit], request()->except('page', 'limit')));

			return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url_bit, 'page' => $page, 'totalPage' => $lastPage]);
		}
	}

	public function show($id)
	{
		$cash = BalanceCash::find($id);

		//return view('backend-distribution::cash.show', compact('cash'));

		return LaravelAdmin::content(function (Content $content) use ($cash) {

			$content->header('查看提现详情');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '佣金提现管理', 'url' => 'distribution/cash?status=STATUS_AUDIT', 'no-pjax' => 1],
				['text' => '查看提现详情', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '佣金提现管理']

			);

			$content->body(view('backend-distribution::cash.show', compact('cash')));
		});
	}

	public function review()
	{
		$cash         = BalanceCash::find(request('id'));
		$cash->status = request('status');
		$cash->save();

		//如果审核不通过,则将佣金返回
		if (request('status') == 3) {
			/*Balance::create([
				'user_id' => $cash->user_id,
				'type' => 'cash_return',
				'note' => '余额提现返回',
				'value' => $cash->amount * 100,
				'origin_id' => $cash->id,
				'origin_type' => 'GuoJiangClub\Component\Balance\Model\BalanceCash'
			]);*/

			$agent                   = Agent::find($cash->agent_id);
			$agent->total_commission = $agent->total_commission + $cash->amount * 100;
			$agent->save();
		}

		//TODO::The operation of money

		//TODO::Restore balance

		return $this->ajaxJson();
	}

	public function operatePay($id)
	{
		$cash = BalanceCash::find($id);

		//return view('backend-distribution::cash.review', compact('cash'));

		return LaravelAdmin::content(function (Content $content) use ($cash) {

			$content->header('查看提现详情');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '佣金提现管理', 'url' => 'distribution/cash?status=STATUS_AUDIT', 'no-pjax' => 1],
				['text' => '查看提现详情', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '佣金提现管理']

			);

			$content->body(view('backend-distribution::cash.review', compact('cash')));
		});
	}

	/**
	 * 打款处理
	 *
	 * @return mixed
	 */
	public function applyPay()
	{
		$cash = BalanceCash::find(request('id'));

		if ($cash->cash_type == 'customer_account') { //如果是打款到用户自定义账户
			if (!$status = request('status')) {
				return $this->ajaxJson(false, [], 404, '请先确认已打款');
			}

			if (!$settle_time = request('settle_time')) {
				return $this->ajaxJson(false, [], 404, '请输入打款时间');
			}

			$cash->status      = $status;
			$cash->settle_time = $settle_time;
			$cash->cert        = request('cert');
			$cash->save();
		} else {
			$result = $this->pay($cash);
			if (!$result['status']) {
				return $this->ajaxJson(false, [], 404, $result['message']);
			}
		}

		return $this->ajaxJson();
	}

	/*private function pay($cash)
	{
		$channel = 'wechat';
		$uid     = auth()->guard('admin')->user()->id;

		if (MerchantPay::where('origin_type', 'COMMISSION')
			->where('origin_id', $cash->id)
			->where('channel', 'wechat')
			->where('status', 'SUCCESS')->first()
		) {
			return ['status' => false, 'message' => '该提现申请已打款'];
		}

		$userBind = UserBind::ByAppID($cash->user_id, 'wechat', settings('wechat_pay_app_id'))->first();
		if (!$userBind) {
			$userBind = UserBind::ByAppID($cash->user_id, 'miniprogram', settings('ibrand_miniapp_pay_miniapp_id'))->first();
			if (!$userBind) {
				return ['status' => false, 'message' => '无法匹配微信用户'];
			}
		}

		if ($userBind->type == 'wechat' AND
			(!Storage::disk('share')->exists('ibrand_wechat_pay_apiclient_key.pem') OR
				!Storage::disk('share')->exists('ibrand_wechat_pay_apiclient_cert.pem'))) {
			return ['status' => false, 'message' => '微信支付证书文件不存在'];
		}

		if ($userBind->type == 'miniprogram' AND
			(!Storage::disk('share')->exists('ibrand_mini_pay_apiclient_key.pem') OR
				!Storage::disk('share')->exists('ibrand_mini_pay_apiclient_cert.pem'))) {
			return ['status' => false, 'message' => '微信支付证书文件不存在'];
		}

		$merchantPayData = [
			'partner_trade_no' => build_order_no('MPR'),
			'openid'           => $userBind->open_id, //收款人的openid
			'check_name'       => 'NO_CHECK',
			'amount'           => $cash->amount * 100,
			'desc'             => '佣金提现：' . $cash->cash_no . '打款',
		];
		$extra           = [
			'origin_id'  => $cash->id,
			'channel'    => $channel,
			'channel_id' => $cash->id,
			'user_id'    => $cash->user_id,
			'admin_id'   => $uid,
		];

		$result = $this->paymentService->pay($merchantPayData, $extra, 'COMMISSION', $userBind->type);
		if (!$result) {
			return ['status' => false, 'message' => '打款失败，请重试'];
		}

		if ($result['result_code'] == 'FAIL') {
			return ['status' => false, 'message' => $result['err_code_des']];
		}

		$cash->status      = 2;
		$cash->settle_time = $result['payment_time'];
		$cash->save();

		return ['status' => true, 'message' => ''];
	}*/
}