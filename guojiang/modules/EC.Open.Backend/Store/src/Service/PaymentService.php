<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Service;

use EasyWeChat\Factory;
use GuoJiangClub\EC\Open\Backend\Store\Model\MerchantPay;
use GuoJiangClub\EC\Open\Backend\Store\Model\MultiGrouponUsers;
use GuoJiangClub\EC\Open\Backend\Store\Processor\MultiGrouponRefundProcessor;

class PaymentService
{
	protected $config;

	protected $payment;
	protected $merchantPay;
	protected $multiGrouponRefundProcessor;

	public function __construct(MultiGrouponRefundProcessor $multiGrouponRefundProcessor)
	{
		$this->multiGrouponRefundProcessor = $multiGrouponRefundProcessor;
	}

	public function pay($payData, $extra, $type = 'REFUND', $channel = 'wechat')
	{
		\Log::info('refund_payData:' . json_encode($payData));
		$merchantPayData                     = $payData;
		$merchantPayData['spbill_create_ip'] = request()->getClientIp();
		$this->merchantPay                   = Factory::payment($this->getConfig($channel))->transfer;
		$result                              = $this->merchantPay->toBalance($merchantPayData);
		\Log::info('refund_pay_result:' . json_encode($result));
		if ($result['return_code'] == 'FAIL' AND $result['err_code'] != 'SYSTEMERROR') {
			return false;
		}
		\Log::info('refund_pay_result2:' . json_encode($result));

		MerchantPay::create([
			'origin_type'      => $type,
			'origin_id'        => $extra['origin_id'],
			'partner_trade_no' => $merchantPayData['partner_trade_no'],
			'payment_no'       => isset($result['payment_no']) ? $result['payment_no'] : '',
			'channel'          => $extra['channel'],
			'channel_id'       => $extra['channel_id'],
			'amount'           => $merchantPayData['amount'],
			'status'           => $result['result_code'],
			'error_code'       => isset($result['error_code']) ? $result['error_code'] : '',
			'err_code_des'     => isset($result['err_code_des']) ? $result['err_code_des'] : '',
			'payment_time'     => isset($result['payment_time']) ? $result['payment_time'] : null,
			'user_id'          => $extra['user_id'],
			'admin_id'         => $extra['admin_id'],
		]);

		if ($result['result_code'] == 'FAIL' AND $result['err_code'] == 'SYSTEMERROR') {
			$search = $result = $this->merchantPay->queryBalanceOrder($merchantPayData['partner_trade_no']);
			if ($search['return_code'] == 'SUCCESS'
				AND $search['result_code'] == 'SUCCESS'
				AND ($search['status'] == 'SUCCESS' OR $search['status'] == 'PROCESSING')
			) {
				$result['result_code'] = 'SUCCESS';
			}
		}

		return $result;
	}

	protected function getConfig($channel)
	{
		if ($channel == 'wechat') {
			$config = [
				'app_id'    => settings('ibrand_wechat_pay_app_id'),
				'mch_id'    => settings('ibrand_wechat_pay_mch_id'),
				'key'       => settings('ibrand_wechat_pay_key'),
				'cert_path' => storage_path('share/ibrand_wechat_pay_apiclient_cert.pem'),
				'key_path'  => storage_path('share/ibrand_wechat_pay_apiclient_key.pem'),
			];
		} else {
			$config = [
				'app_id'    => settings('ibrand_miniapp_pay_miniapp_id'),
				'mch_id'    => settings('ibrand_miniapp_pay_mch_id'),
				'key'       => settings('ibrand_miniapp_pay_key'),
				'cert_path' => storage_path('share/ibrand_mini_pay_apiclient_cert.pem'),
				'key_path'  => storage_path('share/ibrand_mini_pay_apiclient_key.pem'),
			];
		}

		return $config;
	}

	public function multiGrouponRefund($groupon_item_id)
	{
		$users = MultiGrouponUsers::where('multi_groupon_items_id', $groupon_item_id)->where('status', 1)->get();
		if (count($users) > 0) {
			foreach ($users as $user) {
				if ($user->refund_status == 0) {
					$this->multiGrouponRefundProcessor->refund($user);
				}
			}
		}
	}
}