<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\Distribution\Backend\Models\AgentCommission;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class CommissionController extends Controller
{

	public function index()
	{
		$agent_id   = request('id');
		$commission = AgentCommission::where('agent_id', $agent_id)->paginate(15);


		return LaravelAdmin::content(function (Content $content) use ($commission) {

			$content->body(view('backend-distribution::commission.index', compact('commission')));
		});
	}
}