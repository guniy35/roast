<?php

namespace GuoJiangClub\EC\Open\Core\Listeners;

use iBrand\Common\Wechat\Factory;
use GuoJiangClub\Component\User\Models\UserBind;

class TemplateMessageEventListener
{
	public function rechargeSuccess($order, $formId)
	{
		$userBind = $this->getUserBind($order->user_id);
		if (!$userBind) {
			return false;
		}

		$data = [
			'thing1'  => $order->recharge->title,
			'amount2' => number_format($order->pay_amount / 100, 2, '.', '') . '元',
			'amount3' => number_format($order->amount / 100, 2, '.', '') . '元',
			'time4'   => $order->pay_time,
		];

		$miniProgram = Factory::miniProgram();
		$miniProgram->template_message->send([
			'touser'      => $userBind->open_id,
			'template_id' => 'jmfBtiip4EB9MNT6m4nJI_6KY02xMBw9-8TscQ3JvM0',
			'page'        => 'pages/recharge/balance/balance',
			'form_id'     => $formId,
			'data'        => $data,
		]);
	}

	public function paidSuccess($order, $formId)
	{
		$userBind = $this->getUserBind($order->user_id);
		if (!$userBind) {
			return false;
		}

		$goods_name = '';
		foreach ($order->getItems() as $item) {
			$goods_name .= $item->item_name . ',';
		}

		$data = [
			'character_string1' => $order->order_no,
			'date2'             => $order->pay_time,
			'thing3'            => trim($goods_name, ','),
			'amount4'           => $order->total_yuan . '元',
			'phrase5'           => '待发货',
		];

		$miniProgram = Factory::miniProgram();
		$miniProgram->template_message->send([
			'touser'      => $userBind->open_id,
			'template_id' => 'oQi9rc1bQL4ZpkGSVcXbr0iGHu4Zcw7QvmF2y2Ljk5o',
			'page'        => 'pages/order/index/index',
			'form_id'     => $formId,
			'data'        => $data,
		]);
	}

	public function getUserBind($user_id)
	{
		$userBind = UserBind::ByAppID($user_id, 'miniprogram', config('ibrand.wechat.miniprogram.default.app_id'))->first();
		if (!$userBind) {
			return false;
		}

		return $userBind;
	}

	public function subscribe($events)
	{
		$events->listen(
			'recharge.success.template.message',
			'GuoJiangClub\EC\Open\Core\Listeners\TemplateMessageEventListener@rechargeSuccess'
		);

		$events->listen(
			'paid.success.template.message',
			'GuoJiangClub\EC\Open\Core\Listeners\TemplateMessageEventListener@paidSuccess'
		);
	}
}