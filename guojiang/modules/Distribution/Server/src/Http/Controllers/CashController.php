<?php

namespace GuoJiangClub\Distribution\Server\Http\Controllers;

use GuoJiangClub\Component\Balance\BalanceCash;
//use GuoJiangClub\Component\BankAccount\Model\BankAccount;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\Distribution\Server\Repository\AgentCommissionRepository;
use GuoJiangClub\EC\Open\Server\Transformers\BalanceCashTransformer;

class CashController extends Controller
{
	protected $commissionRepository;

	public function __construct(AgentCommissionRepository $agentCommissionRepository)
	{
		$this->commissionRepository = $agentCommissionRepository;
	}

	/**
	 * 提现记录列表
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function getCashList()
	{
		$limit = request('limit') ? request('limit') : 15;
		$uid   = request()->user()->id;

		$list = BalanceCash::where('user_id', $uid)->orderBy('created_at', 'desc')->paginate($limit);

		return $this->response()->paginator($list, new BalanceCashTransformer());
	}

	/**
	 * 提交提现申请
	 *
	 */
	public function applyCash()
	{
		$user = request()->user();

		$amount          = request('amount') * 100;
		$bank_account_id = request('bank_account_id');
		$data            = [
			'amount'  => $amount,
			'user_id' => $user->id,
			'status'  => 0,
		];

		if (!$agent = Agent::where('user_id', $user->id)->first() OR $agent->status != 1) {
			return $this->api([], false, 500, '您的身份不存在');
		}

		$amountCount = $this->commissionRepository->getSumCommission($agent->id);//所有佣金
		$cash        = BalanceCash::where('agent_id', $agent->id)->where('status', '<>', 3)->sum('amount'); //已提现的
		$balance     = ($amountCount - $cash) - $amount;

		if ($amountCount == 0 OR $balance < 0) {
			return $this->response()->errorBadRequest('提现金额错误');
		}

		/*if ($bank_account_id) {
			if (!$bankAccount = BankAccount::find($bank_account_id)) {
				return $this->response()->errorBadRequest('提现账号错误');
			}

			$data['bank_account_id'] = $bank_account_id;
			$data['bank_number']     = $bankAccount->bank_card_number;
			$data['owner_name']      = $bankAccount->owner_name;
			$data['bank_name']       = $bankAccount->bank->bank_name;
		}*/

		$data['cash_type'] = request('cash_type');
		$data['balance']   = $balance;
		$data['agent_id']  = $agent->id;
		$data['cash_no']   = build_order_no('C');

		$cash = BalanceCash::create($data);

		$agent->total_commission = $balance;
		$agent->save();

		return $this->api($cash);
	}

	public function balanceSum()
	{
		$user = request()->user();
		if (!$agent = Agent::where('user_id', $user->id)->first() OR $agent->status != 1) {
			return $this->response()->errorBadRequest('您的身份不存在');
		}

		$limit = settings('distribution_limit') ? settings('distribution_limit') : 10;

		$amountCount = $this->commissionRepository->getSumCommission($agent->id);//所有佣金
		$cash        = BalanceCash::where('agent_id', $agent->id)->where('status', '<>', 3)->sum('amount'); //已提现的
		$sumBalance  = $amountCount - $cash;

		return $this->api(['sumBalance' => $sumBalance, 'limit' => $limit]);
	}

}