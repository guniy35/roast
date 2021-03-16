<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Processor;

use Carbon\Carbon;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\Component\Payment\Models\PaymentLog;
use GuoJiangClub\Component\Payment\Services\RefundService;
use GuoJiangClub\EC\Open\Backend\Store\Model\MerchantPay;
use GuoJiangClub\EC\Open\Backend\Store\Model\Order;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGrouponUsers;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/8/2
 * Time: 10:26
 */
class MultiGrouponRefundProcessor
{
	private   $balanceRepository;
	protected $paymentRefund;

	public function __construct(BalanceRepository $balanceRepository,
	                            RefundService $paymentRefundService)
	{
		$this->balanceRepository = $balanceRepository;
		$this->paymentRefund     = $paymentRefundService;
	}

	public function test()
	{
		\Pingpp\Pingpp::setApiKey($this->getApiKey());
		$this->setPrivateKey();
		$ch = \Pingpp\Charge::retrieve('ch_S08SKCP4C4eTnz9uX1ur904G');
		$re = $ch->refunds->create([
			'amount'      => 100,
			'description' => '拼团失败退款',
			'metadata'    => [
				'partner_trade_no' => build_order_no('MPR'),
				'user_id'          => 1,
			],
		]);
		\Log::info($re);
	}

	public function refund($grouponItemUser)
	{
		$order = Order::find($grouponItemUser->order_id);

		if (!$order OR $order->status != 6 OR $order->user_id != $grouponItemUser->user_id OR $order->type != 10) {
			return;
		}

		$payments = $order->payments()->where('status', 'completed')->get();

		if (count($payments) == 0) {
			return;
		}

		$flag = false;
		foreach ($payments as $payment) {
			if ($payment->channel == 'balance') {   //如果是余额支付,需要退款到余额

				$data    = ['user_id'     => $grouponItemUser->user_id,
				            'type'        => 'groupon_refund',
				            'note'        => '拼团失败退款：' . $payment->amount / 100 . '元',
				            'value'       => $payment->amount,
				            'origin_id'   => $grouponItemUser->id,
				            'origin_type' => MultiGrouponUsers::class,
				];
				$balance = $this->balanceRepository->addRecord($data);
				MerchantPay::create([
					'origin_type'      => 'GROUPON_REFUND',
					'origin_id'        => $grouponItemUser->id,
					'partner_trade_no' => build_order_no('MPR'),
					'payment_no'       => '',
					'channel'          => 'balance',
					'channel_id'       => $balance->id,
					'amount'           => $payment->amount,
					'status'           => 'SUCCESS',
					'error_code'       => '',
					'err_code_des'     => '',
					'payment_time'     => Carbon::now(),
					'user_id'          => $grouponItemUser->user_id,
					'admin_id'         => 1,
				]);
				$flag = true;
			} else {
				if (!settings('enabled_pingxx_pay')) { //如果不是pingxx支付
					$result = $this->originalRefund($order);
					if (count($result) == 0) {
						$flag = false;
					} else {
						$flag = true;
						$this->paymentRefund->createPaymentRefundLog('create_refund', Carbon::now(), $result['refund_no'], $result['order_no'], $result['refund_id'], $result['amount'], $result['channel'], $result['type'], 'SUCCESS', $result['meta']);
						MerchantPay::create([
							'origin_type'      => 'GROUPON_REFUND',
							'origin_id'        => $grouponItemUser->id,
							'partner_trade_no' => $result['refund_no'],
							'payment_no'       => $result['refund_id'],
							'channel'          => $result['channel'],
							'channel_id'       => 0,
							'amount'           => $result['amount'],
							'status'           => 'SUCCESS',
							'error_code'       => '',
							'err_code_des'     => '',
							'payment_time'     => Carbon::now(),
							'user_id'          => $grouponItemUser->user_id,
							'admin_id'         => 1,
						]);
					}
				} else {
					\Pingpp\Pingpp::setApiKey($this->getApiKey());
					$this->setPrivateKey();
					$ch = \Pingpp\Charge::retrieve($payment->pingxx_no);
					$re = $ch->refunds->create([
						'amount'      => $payment->amount,
						'description' => '拼团失败退款',
						'metadata'    => [
							'partner_trade_no' => build_order_no('MPR'),
							'user_id'          => $grouponItemUser->user_id,
						],
					]);
					\Log::info($re);
					if (($re->succeed AND $re->status == 'succeeded') OR $re->status == 'pending') {
						$flag = true;
						MerchantPay::create([
							'origin_type'      => 'GROUPON_REFUND',
							'origin_id'        => $grouponItemUser->id,
							'partner_trade_no' => $re->metadata->partner_trade_no,
							'payment_no'       => $re->transaction_no,
							'channel'          => 'pingxx',
							'channel_id'       => 0,
							'amount'           => $payment->amount,
							'status'           => $re->status == 'pending' ? 'PENDING' : 'SUCCESS',
							'error_code'       => '',
							'err_code_des'     => '',
							'payment_time'     => $re->time_succeed ? Carbon::createFromTimestamp($re->time_succeed) : Carbon::now(),
							'user_id'          => $grouponItemUser->user_id,
							'admin_id'         => 1,
						]);
					} elseif ($re->status == 'failed') {
						$flag = false;
						MerchantPay::create([
							'origin_type'      => 'GROUPON_REFUND',
							'origin_id'        => $grouponItemUser->id,
							'partner_trade_no' => $re->metadata->partner_trade_no,
							'payment_no'       => $re->transaction_no,
							'channel'          => 'pingxx',
							'channel_id'       => 0,
							'amount'           => $payment->amount,
							'status'           => 'FAILED',
							'error_code'       => $re->failure_code,
							'err_code_des'     => $re->failure_msg,
							'payment_time'     => $re->time_succeed ? Carbon::createFromTimestamp($re->time_succeed) : Carbon::now(),
							'user_id'          => $grouponItemUser->user_id,
							'admin_id'         => 1,
						]);
					}
				}
			}
		}

		if ($flag) {
			$grouponItemUser->refund_status = 1;
			$grouponItemUser->save();
		}
	}

	private function getApiKey()
	{
		if (settings('pingxx_pay_scene') AND settings('pingxx_pay_scene') == 'live' AND $apiKey = settings('pingxx_live_secret_key')) {
			return $apiKey;
		}

		if ($apiKey = settings('pingxx_test_secret_key')) {
			return $apiKey;
		}

		return config('payment.pingxx_live_secret_key');
	}

	private function setPrivateKey()
	{
		\Pingpp\Pingpp::setPrivateKeyPath(storage_path('share') . '/rsa_private_key.pem');
	}

	protected function originalRefund($order)
	{
		$paymentLog = PaymentLog::where('order_no', $order->order_no)
			->where(function ($query) {
				$query->where('action', 'result_pay')->orWhere('action', 'query_result_pay');
			})->get()->last();

		if (!$paymentLog) {
			return [];
		}

		$refundNo = str_replace('O', 'RMUTI', $order->order_no);
		$this->paymentRefund->createPaymentRefundLog('apply_multiGroupon_refund', Carbon::now(), $refundNo, $order->order_no, '', $paymentLog->amount, $paymentLog->channel, 'order', 'SUCCESS', []);

		$description = '订单：' . $order->order_no . '拼团失败退款';
		$result      = $this->paymentRefund->createRefund($order->order_no, $paymentLog->transcation_order_no, $refundNo, $paymentLog->amount, $paymentLog->amount, $paymentLog->channel, $description);

		return $result;
	}

}