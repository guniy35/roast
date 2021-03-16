<?php

namespace GuoJiangClub\Distribution\Server\Http\Controllers;

use GuoJiangClub\Component\Suit\Repositories\SuitRepository;
use GuoJiangClub\Distribution\Core\Models\Agent;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;
use GuoJiangClub\Component\User\Models\User;

class SuitController extends Controller
{
	protected $suitRepository;
	protected $miniQrcode;

	public function __construct(SuitRepository $suitRepository)
	{
		$this->suitRepository = $suitRepository;
	}

	/**
	 * 套餐分享页面获取分享基础信息
	 *
	 * @param $suitID
	 * @param $agent_code
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function shareBase($suitID)
	{
		$user  = request()->user();
		$agent = Agent::where('user_id', $user->id)->first();
		if (!$agent OR !$agent->status) {
			return $this->api([], false, 500, '您尚未注册成为分销商');
		}

		if (!$suit = $this->suitRepository->getSuitById($suitID)) {
			return $this->api([], false, 500, '套餐不存在');
		}
		$rate       = settings('distribution_rate');
		$percentage = ($suit->rate / 100) * ($rate[0]['value'] / 100);
		$commission = round($suit->total * $percentage, 2);

		return $this->api([
			'commission' => $commission,
			'user_name'  => $user->nick_name,
			'avatar'     => $user->avatar,
			'agent_code' => $agent->code,
			'Suit'       => $suit,
		]);
	}

	/**
	 * 获取分享的海报
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function shareImage()
	{
		$user       = request()->user();
		$agent_code = request('agent_code');
		$suitID     = request('suit_id');
		$type       = request('type');

		$suit = $this->suitRepository->getSuitById($suitID);
		if (!$suit) {
			return $this->api([], false, 500, '套餐不存在');
		}

		$agent = Agent::where('code', $agent_code)->where('user_id', $user->id)->first();
		if (!$agent OR !$agent->status) {
			return $this->api([], false, 500, '您尚未注册成为分销商');
		}

		$route = url('api/distribution/Suit/template?user_id=' . $user->id . '&suit_id=' . $suit->id . '&agent_code=' . $agent_code . '&type=' . $type);
		$data  = MiniProgramShareImg::run($suit, $route, true);
		if ($data) {
			return $this->api(['img' => $data['url']]);
		}

		return $this->api([], false, 400, '生成失败');
	}

	public function getSuitTemplate()
	{
		$user_id    = request('user_id');
		$suit_id    = request('suit_id');
		$agent_code = request('agent_code');
		$type       = request('type');
		$user       = User::find($user_id);
		if ($type == 'miniProgram') {
			$save_path = 'public/agent/share/Suit/' . $suit_id . '/' . $user_id . '_qrcode.png';
			$mini      = $this->miniQrcode->createMiniQrcode('pages/store/meal/meal', 280, $save_path, 'B', $suit_id . ',' . $agent_code);
			if ($mini) {
				$qr_code = env('APP_URL') . '/storage/agent/share/Suit/' . $suit_id . '/' . $user_id . '_qrcode.png';
			} else {
				return false;
			}
		} else {
			$qr_code_url = settings('mobile_domain_url') . '/#!/store/meal/' . $suit_id . '?agent_code=' . $agent_code;
			$qr_code     = storage_path('app/public/agent/share/Suit/' . $suit_id . '/' . $user_id . '.png');
			\QrCode::format('png')->size(152)->margin(1)->generate($qr_code_url, $qr_code);
			$qr_code = env('APP_URL') . '/storage/agent/share/Suit/' . $suit_id . '/' . $user_id . '.png';
		}

		if (!$qr_code) {
			return $this->api([], false, 200, '生成失败');
		}

		$agent  = Agent::where('code', $agent_code)->where('user_id', $user->id)->first();
		$suit   = $this->suitRepository->getSuitById($suit_id);
		$avatar = $user->avatar ? $user->avatar : public_path('assets/backend/distribution/no_head.jpg');

		return view('distribution-server::share.Suit', compact('agent', 'suit', 'avatar', 'user', 'qr_code'));
	}
}