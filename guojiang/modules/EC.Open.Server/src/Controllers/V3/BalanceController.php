<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use Carbon\Carbon;
use GuoJiangClub\Component\Balance\Balance;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\Component\Recharge\Models\BalanceOrder;
use GuoJiangClub\EC\Open\Server\Transformers\BalanceTransformer;
use GuoJiangClub\Component\Recharge\Repositories\RechargeRuleRepository;
use iBrand\Component\Pay\Contracts\PayChargeContract;

class BalanceController extends Controller
{
	protected $balanceRepository;
	protected $rechargeRuleRepository;
	protected $charge;

	public function __construct(BalanceRepository $balanceRepository, RechargeRuleRepository $rechargeRuleRepository, PayChargeContract $charge)
	{
		$this->balanceRepository      = $balanceRepository;
		$this->rechargeRuleRepository = $rechargeRuleRepository;
		$this->charge                 = $charge;
	}

	public function index()
	{
		$type     = request('type');
		$limit    = request('limit') ? request('limit') : 15;
		$balances = $this->balanceRepository->fluctuation(request()->user()->id, $type)->paginate($limit);

		return $this->response()->paginator($balances, new BalanceTransformer());
	}

	public function sum()
	{
		$user = request()->user();
		$sum  = Balance::sumByUser($user->id);
		if (!is_numeric($sum)) {
			$sum = 0;
		} else {
			$sum = (int) $sum;
		}

		return $this->success(compact('sum'));
	}

	public function getSchemes()
	{
		$lists = $this->rechargeRuleRepository->getEffective();

		return $this->success($lists);
	}

	public function charge()
	{
		$user    = request()->user();
		$channel = request('channel');
		if (!$this->rechargeRuleRepository->getEffectiveByID(request('recharge_rule_id'), request('pay_amount'), request('amount'))) {
			return $this->failed('储值优惠不存在');
		}

		$order = BalanceOrder::create([
			'user_id'          => $user->id,
			'order_no'         => build_order_no('R'),
			'pay_type'         => $channel,
			'amount'           => request('amount'),
			'pay_amount'       => request('pay_amount'),
			'recharge_rule_id' => request('recharge_rule_id'),
		]);

		$redirect_url = $this->getRedirectUrl($order->order_no);
		$amount       = $order->pay_amount;
		$subject      = '余额充值';
		$body         = '余额充值';
		$order_no     = $order->order_no;

		if ($channel == 'wx_pub_qr') {
			$charge = $this->charge->create([
				'channel'     => 'wx_pub_qr',
				'order_no'    => $order_no,
				'amount'      => $amount,
				'body'        => $body,
				'subject'     => $subject,
				'client_ip'   => request()->getClientIp(),
				'extra'       => request('extra'),
				'time_expire' => request('time_expire'),
				'metadata'    => request('metadata'),
				'description' => request('description'),
			], 'recharge');

			return $this->success(compact('charge'));
		}

		if (request('channel') == 'wx_lite') {
			$charge = $this->charge->create([
				'channel'     => 'wx_lite',
				'order_no'    => $order_no,
				'amount'      => $amount,
				'body'        => $body,
				'subject'     => $subject,
				'client_ip'   => request()->getClientIp(),
				'extra'       => request('extra'),
				'time_expire' => request('time_expire'),
				'metadata'    => request('metadata'),
				'description' => request('description'),
			], 'recharge');

			return $this->success(compact('charge', 'name'));
		}

		return $this->success(compact('redirect_url'));
	}

	public function paid()
	{
		$user     = request()->user();
		$order_no = request('order_no');
		if (!$order_no || !$order = BalanceOrder::where('order_no', $order_no)->with('recharge')->first()) {
			return $this->failed('订单不存在');
		}

		$sum = Balance::sumByUser($user->id);
		if (!is_numeric($sum)) {
			$sum = 0;
		} else {
			$sum = (int) $sum;
		}

		if (settings('pingxx_pay_scene') == 'test') {

			if ($order AND $order->pay_status == 0 AND $order->pay_amount == request('pay_amount')) {
				$order->pay_status = 1;
				$order->pay_time   = Carbon::now();
				$order->save();

				Balance::create(['user_id' => $order->user_id, 'type' => 'recharge', 'note' => '充值', 'value' => $order->amount, 'current_balance' => $sum + $order->amount, 'origin_id' => $order->id, 'origin_type' => BalanceOrder::class]);
			}
		}

		event('user.recharge.point', [$order]);
		event('user.recharge.coupon', [$order]);
		event('recharge.success.template.message', [$order, request('form_id')]);

		return $this->success(compact('order', 'sum'));
	}

	private function getRedirectUrl($order_no)
	{
		$type    = 'recharge';
		$balance = request('balance');

		$channel = request('channel');
		if ($channel == 'alipay_wap') {
			return route('ali.pay.charge', compact('channel', 'type', 'order_no', 'balance'));
		}

		if ($channel == 'wx_pub') {
			return route('wechat.pay.getCode', compact('channel', 'type', 'order_no', 'balance'));
		}
	}
}
