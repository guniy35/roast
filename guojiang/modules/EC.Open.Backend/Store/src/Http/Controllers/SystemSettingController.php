<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use Illuminate\Http\Request;
use iBrand\Backend\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class SystemSettingController extends Controller
{

	public function refundReason()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('售后设置');

			$content->breadcrumb(
				['text' => '商城设置', 'url' => 'store/setting/shopSetting', 'no-pjax' => 1],
				['text' => '售后设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '售后设置']

			);

			$content->body(view('store-backend::setting.refund_reason'));
		});
	}

	/**
	 * 保存退换货理由
	 *
	 * @param Request $request
	 */
	public function saveRefundSettings(Request $request)
	{
		$data = $request->except('_token');

		if (!isset($data['order_refund_reason'])) {
			return $this->ajaxJson(false, [], 400, '退换货理由不能为空');
		}

		$reason = $data['order_refund_reason'];

		foreach ($reason as $item) {
			if (!$item['key'] OR !$item['value']) {
				return $this->ajaxJson(false, [], 400, 'key值或者理由不能为空');
			}
		}

		$array = array_map('array_shift', $reason);
		if (count($array) != count(array_unique($array))) {
			return $this->ajaxJson(false, [], 400, '存在重复的key值');
		}

		settings()->setSetting($data);

		return $this->ajaxJson();
	}

	public function point()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('积分设置');

			$content->breadcrumb(
				['text' => '商城设置', 'url' => 'store/setting/shopSetting', 'no-pjax' => 1],
				['text' => '积分设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '积分设置']

			);

			$content->body(view('store-backend::setting.point'));
		});
	}

	public function shopSetting()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('商城设置');

			$content->breadcrumb(
				['text' => '商城设置', 'url' => 'store/setting/shopSetting', 'no-pjax' => 1],
				['text' => '商城设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '全局设置']

			);

			$content->body(view('store-backend::setting.shop'));
		});
	}

	public function saveSettings(Request $request)
	{
		$data = ($request->except('_token', 'file'));

		settings()->setSetting($data);

		$this->ajaxJson();
	}

	/**
	 * 客服设置
	 */
	public function onlineService()
	{
		return LaravelAdmin::content(function (Content $content) {

			$content->header('客服设置');

			$content->breadcrumb(
				['text' => '商城设置', 'url' => 'store/setting/onlineService', 'no-pjax' => 1],
				['text' => '客服设置', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '客服设置']
			);

			$content->body(view('store-backend::setting.online_service'));
		});
	}

	public function saveOnlineService(Request $request)
	{
		$data = $request->except(['_token', 'file']);

		if ($data['online_service_status']) {
			if (!isset($data['online_service_type'])) {
				return $this->ajaxJson(false, [], 400, '请选择客服设置类型');
			}
			if ($data['online_service_type'] == 'self') {
				foreach ($data['online_service_self'] as $item) {
					if (!$item) {
						return $this->ajaxJson(false, [], 400, '请完善自有客服信息');
					}
				}
			}

			if ($data['online_service_type'] == 'platform' AND !$data['online_service_url']) {
				return $this->ajaxJson(false, [], 400, '请填写云客服URL');
			}
		}

		settings()->setSetting($data);

		return $this->ajaxJson();
	}
}