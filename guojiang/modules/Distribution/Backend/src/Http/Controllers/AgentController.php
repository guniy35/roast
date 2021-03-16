<?php

namespace GuoJiangClub\Distribution\Backend\Http\Controllers;

use Carbon\Carbon;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Backend\Models\AgentRelation;
use GuoJiangClub\Distribution\Backend\Repository\AgentRepository;
use Illuminate\Http\Request;
use Validator;
use Encore\Admin\Facades\Admin as LaravelAdmin;
use Encore\Admin\Layout\Content;

class AgentController extends Controller
{
	protected $agentRepository;
	protected $cache;

	public function __construct(AgentRepository $agentRepository)
	{
		$this->agentRepository = $agentRepository;
		$this->cache           = cache();
	}

	public function index()
	{
		$condition = $this->setCondition();
		$where     = $condition[0];
		$time      = $condition[1];
		$agents    = $this->agentRepository->getAgentPaginate($where, $time, 15);

		return LaravelAdmin::content(function (Content $content) use ($agents) {

			$content->header('分销员管理列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销员管理', 'url' => 'distribution/agent?status=STATUS_AUDITED', 'no-pjax' => 1],
				['text' => '分销员管理列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.index', compact('agents')));
		});
	}

	protected function setCondition()
	{
		$status = 1;
		switch (request('status')) {
			case 'STATUS_AUDIT':
				$status = 0;
				break;
			case 'STATUS_AUDITED':
				$status = 1;
				break;
			case 'STATUS_FAILED':
				$status = 2;
				break;
			case 'STATUS_RETREAT':
				$status = 3;
				break;
		}
		$where['status'] = $status;

		if ($value = request('value')) {
			$where['filter'] = ['like', '%' . $value . '%'];
		}

		$time = [];
		/*注册时间*/
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
	 * 审核
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function audit($id)
	{
		$agent = $this->agentRepository->find($id);

		//return view('backend-distribution::agent.audit', compact('agent'));

		return LaravelAdmin::content(function (Content $content) use ($agent) {

			$content->header('分销员审核');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销员审核', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.audit', compact('agent')));
		});
	}

	public function edit($id)
	{
		$agent = $this->agentRepository->find($id);

		//return view('backend-distribution::agent.edit', compact('agent'));

		return LaravelAdmin::content(function (Content $content) use ($agent) {

			$content->header('分销员编辑');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销员管理', 'url' => 'distribution/agent?status=STATUS_AUDITED', 'no-pjax' => 1],
				['text' => '分销员编辑', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.edit', compact('agent')));
		});
	}

	/**
	 * 审核分销员
	 *
	 * @return mixed
	 */
	public function saveAgent(Request $request)
	{
		$input = $request->except('_token', 'id');
		$agent = Agent::find(request('id'));
		$agent->update($input);

		return $this->ajaxJson();
	}

	/**
	 * 清退分销员
	 *
	 * @return mixed
	 */
	public function retreatAgent()
	{
		$id            = request('id');
		$agent         = $this->agentRepository->find($id);
		$agent->status = 3;
		$agent->save();

		/*AgentRelation::where('agent_id', $id)->orWhere('parent_agent_id', $id)->delete();*/

		return $this->ajaxJson();
	}

	/**
	 * 还原分销员
	 *
	 * @return mixed
	 */
	public function restoreAgent()
	{
		$id            = request('id');
		$agent         = $this->agentRepository->find($id);
		$agent->status = 1;
		$agent->save();

		return $this->ajaxJson();
	}

	/**
	 * 分销员会员列表
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function agentUsers($id)
	{
		$agent = $this->agentRepository->find($id);
		$users = $agent->manyUsers()->paginate(15);

		//return view('backend-distribution::agent.includes.agent_user', compact('agent', 'users'));

		return LaravelAdmin::content(function (Content $content) use ($agent, $users) {

			$content->header('分销员会员管理列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销员管理', 'url' => 'distribution/agent?status=STATUS_AUDITED', 'no-pjax' => 1],
				['text' => '分销员会员管理列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.includes.agent_user', compact('agent', 'users')));
		});
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

		$agents = $this->agentRepository->getAgentPaginate($where, $time, $limit);

		$lastPage = $agents->lastPage();

		$agentExcelData = $this->agentRepository->formatToExcelData($agents);

		if ($page == 1) {
			session(['export_agents_cache' => generate_export_cache_name('export_agents_cache_')]);
		}
		$cacheName = session('export_agents_cache');

		if ($this->cache->has($cacheName)) {
			$cacheData = $this->cache->get($cacheName);
			$this->cache->put($cacheName, array_merge($cacheData, $agentExcelData), 300);
		} else {
			$this->cache->put($cacheName, $agentExcelData, 300);
		}

		if ($page == $lastPage) {
			$title = ['姓名', '手机', '用户数', '订单数', '累计佣金(元)', '待结算佣金(元)', '加入时间', '状态'];

			return $this->ajaxJson(true, ['status' => 'done', 'url' => '', 'type' => $type, 'title' => $title, 'cache' => $cacheName, 'prefix' => 'agents_data_']);
		} else {
			$url_bit = route('admin.distribution.agent.getExportData', array_merge(['page' => $page + 1, 'limit' => $limit], request()->except('page', 'limit')));

			return $this->ajaxJson(true, ['status' => 'goon', 'url' => $url_bit, 'page' => $page, 'totalPage' => $lastPage]);
		}
	}

	/**
	 * 下级分销员列表
	 */
	public function subAgent()
	{
		$agent_id = request('id');
		$agent    = Agent::find($agent_id);
		$agents   = AgentRelation::where('parent_agent_id', $agent_id)->paginate(15);

		//return view('backend-distribution::agent.sub_agent', compact('agents', 'agent'));

		return LaravelAdmin::content(function (Content $content) use ($agents, $agent) {

			$content->header('分销员管理列表');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '分销员管理', 'url' => 'distribution/agent?status=STATUS_AUDITED', 'no-pjax' => 1],
				['text' => '分销员管理列表', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.sub_agent', compact('agents', 'agent')));
		});
	}

	public function create()
	{
		//return view('backend-distribution::agent.create');

		return LaravelAdmin::content(function (Content $content) {

			$content->header('新增分销员');

			$content->breadcrumb(
				['text' => '分销管理', 'url' => 'distribution', 'no-pjax' => 1],
				['text' => '新增分销员', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '分销员列表']

			);

			$content->body(view('backend-distribution::agent.create'));
		});
	}

	public function store()
	{
		// 表单验证
		$validator = $this->validateForm();
		if ($validator->fails()) {
			$warnings     = $validator->messages();
			$show_warning = $warnings->first();

			return response()->json(['status'       => false
			                         , 'error_code' => 0
			                         , 'message'    => $show_warning,
			]);
		}

		$data         = request()->all();
		$data['code'] = $this->createCode();

		if (!$this->filterName($data['name'])) {
			return $this->ajaxJson(false, [], 400, '分销员姓名不能包含特殊字符');
		}

		if (Agent::where('user_id', $data['user_id'])->first()) {
			return $this->ajaxJson(false, [], 400, '该会员已经是分销员');
		}

		Agent::create($data);

		return $this->ajaxJson(true);
	}

	protected function filterName($name)
	{
		$regex = "/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u";
		if (!preg_match($regex, $name)) {
			return false;
		}

		return true;
	}

	public function searchUser()
	{
		$user = User::where('mobile', 'like', '%' . request('mobile') . '%')->get()->toArray();

		if (count($user)) {
			return $this->ajaxJson(true, ['user' => $user]);
		}

		return $this->ajaxJson(false, [], 400, '未找到任何用户');
	}

	protected function createCode()
	{
		$code = generate_random_string(8);
		if (Agent::where('code', $code)->first()) {
			return $this->createCode();
		}

		return $code;
	}

	protected function validateForm()
	{
		$rules   = [
			'name'    => 'required',
			'mobile'  => 'required|unique:' . config('ibrand.app.database.prefix', 'ibrand_') . 'agent',
			'user_id' => 'required',
			'status'  => 'required',
		];
		$message = [
			'required' => ':attribute 不能为空',
			'unique'   => ':attribute 已经存在',
		];

		$attributes = [
			'name'    => '姓名',
			'mobile'  => '手机号码',
			'user_id' => '关联用户',
			'status'  => '状态',
		];

		$validator = Validator::make(request()->all(), $rules, $message, $attributes);

		return $validator;
	}

}