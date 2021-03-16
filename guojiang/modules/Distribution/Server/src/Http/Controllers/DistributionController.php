<?php

namespace GuoJiangClub\Distribution\Server\Http\Controllers;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\Distribution\Server\Services\AgentsService;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;
use Illuminate\Http\Request;
use Validator;
use GuoJiangClub\Distribution\Server\Repository\AgentRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRelationRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentUserRelationRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentGoodsRepository;
use GuoJiangClub\Distribution\Server\Repository\GoodsRepository;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\EC\Open\Core\Common\MiniProgram as MiniGetWxaCode;
use DB;
use Storage;

class DistributionController extends Controller
{
	protected $agent;
	protected $agentRelation;
	protected $agentUser;
	protected $agentGoods;
	protected $goods;
	protected $balanceRep;
	protected $agentService;
	protected $miniGetWxaCode;

	public function __construct(
		AgentRepository $agentRepository,
		AgentRelationRepository $agentRelationRepository,
		AgentUserRelationRepository $agentUserRelationRepository,
		AgentGoodsRepository $agentGoodsRepository,
		GoodsRepository $goodsRepository,
		BalanceRepository $balanceRepository,
		AgentsService $agentsService,
		MiniGetWxaCode $miniGetWxaCode)
	{
		$this->agent          = $agentRepository;
		$this->agentRelation  = $agentRelationRepository;
		$this->agentUser      = $agentUserRelationRepository;
		$this->agentGoods     = $agentGoodsRepository;
		$this->goods          = $goodsRepository;
		$this->balanceRep     = $balanceRepository;
		$this->agentService   = $agentsService;
		$this->miniGetWxaCode = $miniGetWxaCode;
	}

	/**
	 * 分销商注册
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function register(Request $request)
	{
		$input            = $request->all();
		$input['user_id'] = request()->user()->id;//request()->user()->id
		$rules            = [
			'name'    => 'required',
			'mobile'  => 'required|unique:ibrand_agent,mobile',
			'user_id' => 'required|unique:ibrand_agent,user_id|min:1',
		];
		$error_messages   = [
			'name.required'    => '分销商姓名必须填写',
			'mobile.required'  => '分销商手机号必须填写',
			'mobile.unique'    => '手机号已存在',
			'user_id.unique'   => '您已经注册为分销商',
			'user_id.required' => '您尚未登录',
			'user_id.min'      => '您尚未登录',
		];

		$parent_id         = 0;
		$fromAgent         = '';
		$agentUserRelation = $this->agentUser->findWhere(['user_id' => $input['user_id']])->first();

		if ($agentUserRelation) {
			$fromAgent = $this->agent->find($agentUserRelation->agent_id);
		} elseif (isset($input['agent_code']) AND $input['agent_code']) {
			$fromAgent = $this->agent->findWhere(['code' => $input['agent_code']])->first();
		}

		if ($fromAgent) {
			$parent_id = $fromAgent->id;
		}

		/*if (isset($input['agent_code']) && $input['agent_code']) {
			$fromAgent = $this->agent->findWhere(['code' => $input['agent_code']])->first();
			if ($fromAgent) {
				$parent_id = $fromAgent->id;
			}
		}*/

		$validator = Validator::make($input, $rules, $error_messages);
		$status    = false;
		$code      = 500;
		if ($validator->fails()) {
			$data = $validator->errors()->all();

			return $this->api($data, $status, $code, implode(',', $data));
		}

		$input['code'] = $this->generate_code();

		if (settings('distribution_audit_status') == 2) { //如果申请设置不需要审核
			$input['status'] = 1;
		}

		DB::beginTransaction();
		unset($input['agent_code']);
		$res = $this->agent->create($input);
		if ($res) {
			$this->agentRelation($res->id, $parent_id);

			DB::commit();
		}

		$data   = $res ? [$res->id] : [];
		$status = $res ? true : false;
		$code   = $res ? 200 : 500;
		if (settings('distribution_audit_status') == 1) { //如果申请设置需要审核
			$note = '提交成功！审核通过后，将由分销专员联系您提交相关资料。';
		} else {
			$note = '您已成功获取【' . settings('store_name') . '】的推荐资格，在商品详情页、活动页，点击“我要推荐”按钮，将你心仪的物品推荐给好友，同时可获得更多收益！';
		}

		$message = $res ? $note : '系统繁忙,请重试';

		return $this->api($data, $status, $code, $message);
	}

	protected function generate_code()
	{
		$code  = '';
		$check = true;
		while ($check) {
			$code  = generate_random_string();
			$agent = $this->agent->findWhere(['code' => $code])->first();
			if (!$agent) {
				$check = false;
			}
		}

		return $code;
	}

	/**
	 * 分销商等级关系
	 *
	 * @param int $agent_id  分销商id
	 * @param int $parent_id 上级分销商id
	 *
	 * @return bool
	 */
	public function agentRelation($agent_id, $parent_id = 0)
	{
		if ($parent_id AND settings('distribution_level') > 1) {
			$this->agentRelation->create(['level' => 2, 'parent_agent_id' => $parent_id, 'agent_id' => $agent_id]);
			$agentRelation = $this->agentRelation->findWhere(['agent_id' => $parent_id]);

			if (count($agentRelation) > 0) {
				foreach ($agentRelation as $key => $item) {
//                    $agent = $this->agent->find($agent_id);
					$agent = $this->agent->find($item->parent_agent_id);
					if ($item->level < settings('distribution_level') AND $agent->status == 1) {
						$data = [
							'level'           => $item->level + 1,
							'parent_agent_id' => $item->parent_agent_id,
							'agent_id'        => $agent_id,
						];

						$this->agentRelation->create($data);
					}
				}
			}
		}
	}

	/**
	 * 获取当前分销商id
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function getAgent()
	{
		if (!isset(request()->user()->id)) {
			return null;
		}

		$user_id = request()->user()->id;
		if (!$user_id) {
			return $this->api([], false, 500, '您尚未登录');
		}

		$agent = $this->agent->with('user')->findWhere(['user_id' => $user_id])->first();
		if (!$agent) {
			return $this->api([], false, 500, '您尚未注册成为分销商');
		}

		if (!$agent->status) {
			return $this->api([], false, 500, '您的分销商注册申请尚未通过审核');
		}

		return $agent;
	}

	/**
	 * 生成推广二维码
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function generalize()
	{
		$agent               = $this->getAgent();
		$share_url           = settings('mobile_domain_url') . '/#!/index?agent_code=' . $agent->code . '&agent_scan=1';
		$return['qr_code']   = 'data:image/png;base64,' . base64_encode(\QrCode::format('png')->size(200)->margin(1)->generate($share_url));
		$return['share_url'] = $share_url;

		return $this->api($return, true, 200, '');
	}

	public function shareInfo($goods_no)
	{
		$goodsInfo  = $this->goods->with('photos')->findWhere(['goods_no' => $goods_no])->first();
		$agentGoods = $this->agentGoods->findWhere(['goods_id' => $goodsInfo->id])->first();
		$rate       = settings('distribution_rate');
		$commission = 0;

		if (!$agentGoods) {
			throw new \Exception('该商品未启用分销');
		}

		if ($agentGoods->activity == 1) {
			/*正常情况*/
			$min        = $max = $goodsInfo->sell_price;
			$percentage = ($agentGoods->rate / 100) * ($rate[0]['value'] / 100);
			if ($min_price = $goodsInfo->min_price) {
				$min = $goodsInfo->min_price;
			}
			if ($max_price = $goodsInfo->max_price) {
				$max = $goodsInfo->max_price;
			}
			if ($min == $max) {
				$commission = number_format($min * $percentage, 2);
			} else {
				$commission = number_format($min * $percentage, 2) . ' - ' . number_format($max * $percentage, 2);
			}
		}

		$shareInfo = [
			'goods_img'          => $goodsInfo->photos->sortByDesc('sort')->pluck('url')->toArray(),
			'goods_id'           => $goodsInfo->id,
			'goods_no'           => $goodsInfo->goods_no,
			'goods_name'         => $goodsInfo->name,
			'goods_sell_price'   => $goodsInfo->sell_price,
			'goods_market_price' => $goodsInfo->market_price,
			'goods_min_price'    => $goodsInfo->min_price,
			'goods_max_price'    => $goodsInfo->max_price,
			'commission'         => $commission,
		];

		return $shareInfo;
	}

	/**
	 * 分享页面-agent_code
	 */
	public function share($goods_no, $agent_code)
	{
		$shareInfo = $this->shareInfo($goods_no);
		$agent     = $this->agent->with('user')->findWhere(['code' => $agent_code])->first();
		if ($agent) {
			$shareInfo['user_avatar'] = $agent->user->avatar ? $agent->user->avatar : '';
			/*if ($agent->user->avatar) {
				$result = $this->curlDownload($agent->user->avatar);
				if (200 == $result['status']) {
					$shareInfo['user_avatar'] = 'data:' . $result['header']['Content-Type'] . ';base64,' . base64_encode($result['body']);
				}
			}*/

			$shareInfo['user_name']  = $agent->user->name;
			$shareInfo['agent_code'] = $agent_code;
			/*$shareInfo['qrcode'] = 'data:image/png;base64,' . base64_encode(\QrCode::format('png')->size(200)->margin(1)->generate(settings('mobile_domain_url') . '/#!/store/detail/' . $shareInfo['goods_id'] . '?agent_code=' . $agent_code));*/

			/*$qrCode = 'data:image/png;base64,' . base64_encode(\QrCode::format('png')->size(200)->margin(1)->generate(settings('mobile_domain_url') . '/#!/store/detail/' . $shareInfo['goods_id'] . '?agent_code=' . $agent_code));*/

			Storage::makeDirectory('public/agent/' . $agent->user_id);

			$qr_code = settings('mobile_domain_url') . '/#!/store/detail/' . $shareInfo['goods_id'] . '?agent_code=' . $agent_code;

			\QrCode::format('png')->size(200)->margin(1)
				->generate($qr_code, storage_path('app/public/agent/' . $agent->user_id . '/' . $shareInfo['goods_id'] . '.png'));

			$qrCode = env('APP_URL') . '/storage/agent/' . $agent->user_id . '/' . $shareInfo['goods_id'] . '.png';

			array_splice($shareInfo['goods_img'], 4, 0, $qrCode);
		}

		return $this->api($shareInfo, true, 200, '');
	}

	/**
	 * 分享页面-login
	 */
	public function shareLogin($goods_no)
	{
		$shareInfo = $this->shareInfo($goods_no);
		$agent     = $this->getAgent();
		if ($agent) {
			$shareInfo['user_avatar'] = $agent->user->avatar ? $agent->user->avatar : '';
			/*if ($agent->user->avatar) {
				$result = $this->curlDownload($agent->user->avatar);
				if (200 == $result['status']) {
					$shareInfo['user_avatar'] = 'data:' . $result['header']['Content-Type'] . ';base64,' . base64_encode($result['body']);
				}
			}*/

			$shareInfo['user_name']  = $agent->user->name;
			$shareInfo['agent_code'] = $agent->code;
			$shareInfo['qrcode']     = 'data:image/png;base64,' . base64_encode(\QrCode::format('png')->size(200)->margin(1)->generate(settings('mobile_domain_url') . '/#!/store/detail/' . $shareInfo['goods_id'] . '?agent_code=' . $agent->code));
		}

		return $this->api($shareInfo, true, 200, '');
	}

	/**
	 * 根据code创建与分销员绑定关系
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function createRelation()
	{
		$user = request()->user();
		$code = request('agent_code');
		if ($code) {
			event('agent.user.relation', [$code, $user->id, false]);
		}

		return $this->api();
	}

	/**
	 * 生成商品分享图片
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function createMiniShareImg()
	{
		$goods_id = request('goods_id');
		$goods    = Goods::where('id', $goods_id)->first();
		if (!$goods) {
			return $this->api([], false, 400, '商品不存在');
		}

		$user    = auth('api')->user();
		$user_id = 0;
		$scene   = $goods->id;
		if ($user) {
			$user_id = $user->id;
			//参数说明：1 商品ID，2 分销员code，3 grouponitemid 4 用户ID，用于分享者获得积分
			$scene = $goods->id . ',,,' . $user_id;
			if ($agent = Agent::where(['user_id' => $user_id, 'status' => 1])->first()) {
				$scene = $goods->id . ',' . $agent->code . ',,' . $user_id;
			}
		}

		$app_id    = config('ibrand.wechat.mini_program.default.app_id');
		$img_name  = $scene . '_' . 'share' . '_' . $app_id . '_mini_qrcode.jpg';
		$save_path = 'share/mini/qrcode/' . $img_name;
		$exists    = Storage::disk('public')->exists($save_path);
		if (!$exists) {
            $this->miniGetWxaCode->createMiniQrcode('pages/store/detail/detail', 160, $save_path, 'B', $scene);

		}

		if (!Storage::disk('public')->exists($save_path)) {
			return $this->api([], false, 400, '生成小程序码失败');
		}

		$route = url('api/distribution/template?goods_id=' . $goods_id . '&user_id=' . $user_id);
		$data  = MiniProgramShareImg::run($goods, $route, true);
		if ($data) {
			return $this->api(['image' => $data['url']]);
		}

		return $this->api([], false, 400, '生成失败');
	}

	public function getTemplate()
	{
		$goods    = Goods::find(request('goods_id'));
		$icon_tip = config('store.goods_share_top_tips') ? config('store.goods_share_top_tips') : '力推时尚全能好货';
		$tips     = config('store.goods_share_bottom_tips') ? config('store.goods_share_bottom_tips') : '米尔优选 为您提供全球好货';

		$user  = null;
		$scene = $goods->id;
		if (request('user_id') && request('user_id') > 0) {
			$user = User::find(request('user_id'));
			//参数说明：1 商品ID，2 分销员code，3 grouponitemid 4 用户ID，用于分享者获得积分
			$scene = $goods->id . ',,,' . $user->id;
			if ($agent = Agent::where(['user_id' => $user->id, 'status' => 1])->first()) {
				$scene = $goods->id . ',' . $agent->code . ',,' . $user->id;
			}
		}

		$app_id     = config('ibrand.wechat.mini_program.default.app_id');
		$img_name   = $scene . '_' . 'share' . '_' . $app_id . '_mini_qrcode.jpg';
		$save_path  = 'share/mini/qrcode/' . $img_name;
		$mini_image = Storage::disk('public')->url($save_path);
		$sellPrice  = $goods->sell_price;

		$price = number_format($sellPrice, 2);

		return view('distribution-server::share.goods', compact('goods', 'mini_image', 'icon_tip', 'tips', 'price'));
	}

	/**
	 * 小程序邀请好友信息接口
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function getMiniShareHomeInfo()
	{
		$user  = request()->user();
		$disk  = 'qiniu';
		$data  = [
			'share_img'   => settings('h5-home-page-share-logo'),
			'share_title' => $user->nick_name . settings('miniprogram-home-page-share-title'),
			'agent_code'  => '',
			'agent_mini'  => '',
		];
		$scene = '';
		$agent = Agent::where('user_id', $user->id)->where('status', 1)->first();
		if ($agent) {
			$data['agent_code'] = $agent->code;
			$scene              = $agent->code;
		}

		$save_path = config('app.name') . '/public/agent/share/distribution/' . $user->id . '_new_distribution.jpg';
		if ($disk != 'qiniu') {
			Storage::makeDirectory(config('app.name') . '/public/agent/share/distribution');
		}

		$mini = $this->miniGetWxaCode->createMiniQrcode('pages/index/index/index', 300, $save_path, 'B', $scene, $disk);
		if ($mini) {
			$mini_image = Storage::disk($disk)->url($save_path);

			$data['agent_mini'] = $mini_image;
		}
		$data['show_agent_tips'] = env('MAODA_COMMISSION') ? 0 : 1;

		return $this->api($data);
	}

	/**
	 * 根据code获取分销员基础信息
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function getAgentInfo()
	{
		$code = request('agent_code');
		if (!$code) {
			return $this->api([], false, 400, '获取数据失败');
		}

		$agent = $this->agent->findWhere(['code' => $code])->first();
		if (!$agent) {
			return $this->api([], false, 400, '获取数据失败');
		}

		$agent->home_title = env('MINI_HOME_TITLE');

		return $this->api($agent);
	}

}