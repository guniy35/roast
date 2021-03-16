<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Listeners;

use GuoJiangClub\EC\Open\Backend\Store\Repositories\MultiGrouponItemRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\PaymentService;

class MultiGrouponRefundEventListener
{
	protected $multiGrouponItemRepository;
	protected $paymentService;

	public function __construct(MultiGrouponItemRepository $multiGrouponItemRepository,
	                            PaymentService $paymentService)
	{
		$this->multiGrouponItemRepository = $multiGrouponItemRepository;
		$this->paymentService             = $paymentService;
	}

	public function onGrouponFailRefund($grouponItems)
	{
		foreach ($grouponItems as $item) {
			\Log::info('进入第一步');
			$this->paymentService->multiGrouponRefund($item->id);
		}
	}

	public function subscribe($events)
	{
		$events->listen(
			'multiGroupon.order.fail',
			'GuoJiangClub\EC\Open\Backend\Store\Listeners\MultiGrouponRefundEventListener@onGrouponFailRefund'
		);
	}
}