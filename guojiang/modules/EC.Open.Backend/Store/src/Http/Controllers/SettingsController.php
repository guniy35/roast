<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use iBrand\Common\Controllers\Controller;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
	public function agreement()
	{
		$user_agreement = settings('user_agreement');

		return LaravelAdmin::content(function (Content $content) use ($user_agreement) {

			$content->header('用户协议');

			$content->breadcrumb(
				['text' => '用户协议', 'no-pjax' => 1, 'left-menu-active' => '用户协议']
			);

			$content->body(view('store-backend::settings.agreement', compact('user_agreement')));
		});
	}

	public function saveAgreement(Request $request)
	{
		$data = $request->except('_token', 'file');

		settings()->setSetting($data);

		$this->success();
	}
}