<?php

/*
 * This file is part of ibrand/auth-api.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Auth\Api\Controllers;

use EasyWeChat;
use iBrand\Common\Exceptions\Exception;
use iBrand\Common\Wechat\Factory;
use GuoJiangClub\Component\User\Repository\UserBindRepository;
use GuoJiangClub\Component\User\Repository\UserRepository;
use GuoJiangClub\Component\User\UserService;
use Illuminate\Http\Request;
use Validator;

/**
 * Class MiniProgramLoginController
 *
 * @package iBrand\Auth\Api\Controllers
 */
class MiniProgramLoginController extends Controller
{
	/**
	 * @var UserRepository
	 */
	protected $userRepository;
	/**
	 * @var UserBindRepository
	 */
	protected $userBindRepository;
	/**
	 * @var UserService
	 */
	protected $userService;
	/**
	 * @var
	 */
	protected $miniProgramService;

	/**
	 * MiniProgramLoginController constructor.
	 *
	 * @param UserRepository     $userRepository
	 * @param UserBindRepository $userBindRepository
	 * @param UserService        $userService
	 */
	public function __construct(UserRepository $userRepository, UserBindRepository $userBindRepository, UserService $userService)
	{
		$this->userRepository     = $userRepository;
		$this->userBindRepository = $userBindRepository;
		$this->userService        = $userService;
	}

	/**
	 * 小程序快速登陆，通过 code 换取 openid，如果 openid 绑定了用户则直接登陆，否则返回 openid 给前端
	 *
	 * @return \Illuminate\Http\Response|mixed
	 * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws Exception
	 */
	public function login()
	{
		$code = request('code');

		if (empty($code)) {
			return $this->failed('missing code parameters.');
		}

		$miniProgram = $this->getMiniprogramApp();

		$result = $miniProgram->auth->session($code);

		if (!isset($result['openid'])) {
			return $this->failed('获取openid失败.');
		}

		$openid = $result['openid'];

		//1. unionid 先判断 unionid 是否存在关联用户，如果存在直接返回 token
		if (isset($result['unionid']) && $user = $this->getUserByUnionid($result['unionid'])) {

			//根据 unionid 找到 user_id 为空的设置好 user_id
			$this->userBindRepository->updateUserIdByUnionId($result['unionid'], $user->id);

			$token = $user->createToken($user->id)->accessToken;

			event('user.login', [$user]);

			return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
		}

		//2. openid 不存在相关用户和记录，直接返回 openid
		if (!$userBind = $this->userBindRepository->getByOpenId($openid)) {

			$data = ['open_id' => $openid, 'type' => 'miniprogram', 'app_id' => $this->getMiniprogramAppId()];

			if (isset($result['unionid']) && !empty($result['unionid'])) {
				$data['unionid'] = $result['unionid'];
			}

			$userBind = $this->userBindRepository->create($data);

			return $this->success(['open_id' => $openid]);
		}

		//2. update unionid
		if ($userBind && isset($result['unionid']) && empty($userBind->unionid)) {
			$userBind->unionid = $result['unionid'];
			$userBind->save();
		}

		//2. openid 不存在相关用户，直接返回 openid
		if (!$userBind->user_id) {
			return $this->success(['open_id' => $openid]);
		}

		//3. 绑定了用户,直接返回 token
		$user = $this->userRepository->find($userBind->user_id);

		$token = $user->createToken($user->id)->accessToken;

		event('user.login', [$user]);

		return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
	}

	public function userLogin(Request $request)
	{
		$input      = $request->all();
		$rules      = [
			'openid'     => 'required',
			'avatar'     => 'required',
			'nick_name'  => 'required',
			'agent_code' => 'required',
		];
		$message    = [
			"required" => ":attribute 不能为空",
		];
		$attributes = [
			'openid'     => '用户openid',
			'avatar'     => '用户头像',
			'nick_name'  => '用户昵称',
			'agent_code' => '关联分销商编码',
		];

		$validator = Validator::make($input, $rules, $message, $attributes);
		if ($validator->fails()) {

			return $this->failed($validator->messages()->first());
		}

		$userBind = $this->userBindRepository->getByOpenId($input['openid']);
		if (!$userBind) {
			$userBind = $this->userBindRepository->create([
				'open_id'   => $input['openid'],
				'type'      => 'miniprogram',
				'app_id'    => $this->getMiniprogramAppId(),
				'avatar'    => $input['avatar'],
				'nick_name' => $input['nick_name'],
			]);
		}

		if (!$userBind->user_id) {
			$user = $this->userRepository->create([
				'nick_name' => $input['nick_name'],
				'avatar'    => $input['avatar'],
			]);

			$userBind->user_id = $user->id;
			$userBind->save();
		} else {
			$user = $this->userRepository->find($userBind->user_id);
		}

		$token = $user->createToken($user->id)->accessToken;

		event('user.login', [$user]);

		return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
	}

	/**
	 * @return \Illuminate\Http\Response|mixed
	 * @throws EasyWeChat\Kernel\Exceptions\DecryptException
	 * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws Exception
	 */
	public function mobileLogin()
	{
		//1. get session key.
		$code = request('code');

		$miniProgram = $this->getMiniprogramApp();

		$result = $miniProgram->auth->session($code);

		if (!isset($result['session_key'])) {
			return $this->failed('获取 session_key 失败.');
		}

		$sessionKey = $result['session_key'];

		//2. get phone number.
		$encryptedData = request('encryptedData');

		$iv = request('iv');

		$decryptedData = $miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData);

		if (!isset($decryptedData['purePhoneNumber'])) {
			return $this->failed('获取手机号失败.');
		}

		$mobile = $decryptedData['purePhoneNumber'];

		$isNewUser = false;

		//3. get or create user.
		if (!$user = $this->userRepository->getUserByCredentials(['mobile' => $mobile])) {
			$data      = ['mobile' => $mobile];
			$user      = $this->userRepository->create($data);
			$isNewUser = true;
		}

		$token = $user->createToken($user->id)->accessToken;

		$this->userService->bindPlatform($user->id, request('open_id'), $this->getMiniprogramAppId(), 'miniprogram');

		event('user.login', [$user, $isNewUser]);

		return $this->success(['token_type' => 'Bearer', 'access_token' => $token, 'is_new_user' => $isNewUser]);
	}

	/**
	 * 此方法只使用与支付时，需要根据 code 换取 openid
	 *
	 * @return \Illuminate\Http\Response|mixed
	 * @throws EasyWeChat\Kernel\Exceptions\InvalidConfigException
	 * @throws Exception
	 */
	public function getOpenIdByCode()
	{
		$miniProgram = $this->getMiniprogramApp();

		$code = request('code');

		if (empty($code)) {
			return $this->failed('缺失code');
		}

		$result = $miniProgram->auth->session($code);

		if (!isset($result['openid'])) {
			return $this->failed('获取openid失败.');
		}

		$openid = $result['openid'];

		return $this->success(compact('openid'));
	}

	/**
	 * @return EasyWeChat\MiniProgram\Application
	 *
	 * @throws Exception
	 */
	protected function getMiniprogramApp(): EasyWeChat\MiniProgram\Application
	{
		$app = request('app') ?? 'default';

		if (!config('ibrand.wechat.mini_program.' . $app . '.app_id') or !config('ibrand.wechat.mini_program.' . $app . '.secret')) {
			throw new Exception('please set wechat miniprogram account.');
		}

		$options = [
			'app_id' => config('ibrand.wechat.mini_program.' . $app . '.app_id'),
			'secret' => config('ibrand.wechat.mini_program.' . $app . '.secret'),
		];

		$miniProgram = Factory::miniProgram($options);

		return $miniProgram;
	}

	/**
	 * @return \Illuminate\Config\Repository|mixed
	 */
	protected function getMiniprogramAppId()
	{
		$app = request('app') ?? 'default';

		return config('ibrand.wechat.mini_program.' . $app . '.app_id');
	}

	public function appUnionidLogin()
	{
		$openid  = request('openid');
		$unionid = request('unionid');
		$app_id  = request('app_id');
		if (!$openid || !$unionid || !$app_id) {
			return $this->failed('参数缺失');
		}

		$user = $this->getUserByUnionid($unionid);
		if ($user) {
			$token = $user->createToken($user->id)->accessToken;

			event('user.login', [$user]);

			return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
		}

		$userBind = $this->userBindRepository->getByOpenId($openid);
		if (!$userBind) {

			$data = ['open_id' => $openid, 'type' => 'app', 'app_id' => $app_id, 'unionid' => $unionid];

			$this->userBindRepository->create($data);

			return $this->success(['open_id' => $openid, 'bind_mobile' => 0]);
		}

		if (!$userBind->user_id) {
			return $this->success(['open_id' => $openid, 'bind_mobile' => 0]);
		}

		$user = $this->userRepository->findWhere(['id' => $userBind->user_id])->first();
		if ($user) {
			$token = $user->createToken($user->id)->accessToken;

			event('user.login', [$user]);

			return $this->success(['token_type' => 'Bearer', 'access_token' => $token]);
		}

		return $this->failed('登录失败');
	}

}
