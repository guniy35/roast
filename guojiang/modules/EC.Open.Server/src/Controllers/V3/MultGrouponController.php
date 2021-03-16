<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use Carbon\Carbon;
use DB;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\MultiGroupon\Models\SpecialType;
use GuoJiangClub\Component\MultiGroupon\Repositories\MultiGrouponItemRepository;
use GuoJiangClub\Component\MultiGroupon\Repositories\MultiGrouponRepository;
use GuoJiangClub\Component\MultiGroupon\Service\MultiGrouponService;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\EC\Open\Server\Transformers\MultiGrouponItemTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\MultiGrouponTransformer;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;

class MultGrouponController extends Controller
{
	protected $multiGrouponService;
	protected $multiGrouponItemRepository;
	protected $freeService;
	protected $multiGrouponRepository;

	public function __construct(MultiGrouponService $multiGrouponService,
	                            MultiGrouponItemRepository $multiGrouponItemRepository,
	                            MultiGrouponRepository $multiGrouponRepository)
	{
		$this->multiGrouponService        = $multiGrouponService;
		$this->multiGrouponItemRepository = $multiGrouponItemRepository;
		$this->multiGrouponRepository     = $multiGrouponRepository;
	}

	public function getGrouponUserList()
	{
		$user     = auth('api')->user();
		$userList = $this->multiGrouponService->getGrouponUserList($user, request('goods_id'), request('multi_groupon_item_id'));

		return $this->success($userList);
	}

	public function getGrouponItems()
	{
		$user  = auth('api')->user();
		$items = $this->multiGrouponItemRepository->getGrouponItemList($user, request('multi_groupon_id'), request('multi_groupon_item_id'));

		return $this->response()->paginator($items, new MultiGrouponItemTransformer());
	}

	/**
	 * 拼团详情.
	 */
	public function showItem()
	{
		$item = $this->multiGrouponItemRepository->getGrouponItemByID(request('multi_groupon_item_id'));

		return $this->response()->item($item, new MultiGrouponItemTransformer('show'))->setMeta(['server_time' => Carbon::now()->toDateTimeString()]);
	}

	/**
	 * 小程序分享图片
	 *
	 * @return \Illuminate\Http\Response|mixed
	 */
	public function createShareImage()
	{
		$itemID      = request('multi_groupon_item_id');
		$goods_id    = request('goods_id');
		$user        = request()->user();
		$grouponUser = MultiGrouponUsers::where('user_id', $user->id)->where('multi_groupon_items_id', $itemID)->first();
		if ($grouponUser->share_img) {
			return $this->success(['image' => $grouponUser->share_img]);
		}

		$route = url('api/multiGroupon/template?item_id=' . $itemID . '&user_id=' . $user->id . '&goods_id=' . $goods_id);
		$data  = MiniProgramShareImg::run($grouponUser, $route);
		if ($data) {
			$grouponUser->share_img = $data['url'];
			$grouponUser->save();

			return $this->success(['image' => $data['url']]);
		}

		return $this->failed('生成失败', 400, false);
	}

	public function template()
	{
		$itemID       = request('item_id');
		$user_id      = request('user_id');
		$goods_id     = request('goods_id');
		$user         = User::find($user_id);
		$grouponUser  = MultiGrouponUsers::where('user_id', $user_id)->where('multi_groupon_items_id', $itemID)->first();
		$grouponID    = $grouponUser->multi_groupon_id;
		$multiGroupon = MultiGroupon::find($grouponID);
		$market_price = number_format($multiGroupon->goods->market_price, 2);
		$price        = number_format($multiGroupon->price, 2);

		if ($user->avatar) {
			$circularImg = $user->avatar;
		} else {
			$circularImg = env('APP_URL') . '/assets/backend/free-event/no_head.jpg';
		}

		$mini_qrcode = $this->multiGrouponService->createMiniQrcode('pages/store/detail/detail', 420, $goods_id . ',' . '' . ',' . $itemID, $grouponID, $user);
		if (!$mini_qrcode) {
			return $this->failed('生成二维码失败，请重试', 400, false);
		}

		$mini_qrcode = env('APP_URL') . '/storage/multi-groupon/' . $grouponID . '/' . $user->id . '_' . 'mini_qrcode.jpg';

		return view('server::share.group', compact('circularImg', 'mini_qrcode', 'user', 'multiGroupon', 'market_price', 'price'));
	}

	public function grouponList()
	{
		$limit = request('limit') ? request('limit') : 10;
		$list  = $this->multiGrouponRepository->getGrouponList($limit);

		return $this->response()->paginator($list, new MultiGrouponTransformer());
	}

	public function apply()
	{
		$multi_groupon_id = request('multi_groupon_id');
		if (!$multi_groupon_id) {
			return $this->failed('拼团编号不能为空');
		}

		$multiGroupon = $this->multiGrouponRepository->findWhere(['id' => $multi_groupon_id, 'status' => 1])->first();
		if (!$multiGroupon) {
			return $this->failed('拼团活动不存在');
		}

		$user  = request()->user();
		$agent = Agent::where('user_id', $user->id)->where('status', Agent::STATUS_AUDITED)->first();
		if (!$agent) {
			return $this->failed('您还不是推客');
		}

		try {
			$this->multiGrouponService->checkGrouponStatusByUser($user->id, $multi_groupon_id, null);

			DB::beginTransaction();

			$order = Order::create(['user_id' => $user->id, 'type' => Order::TYPE_VIRTUAL_MULTI_GROUPON, 'items_total' => 0, 'total' => 0, 'pay_status' => 1]);
			SpecialType::create(['order_id' => $order->id, 'origin_type' => 'multi_groupon', 'origin_id' => $multi_groupon_id]);

			event('order.submitted', [$order]);
			event('order.paid', [$order]);

			DB::commit();

			$order->status = Order::STATUS_PAY;
			$order->save();

			$groupUser = MultiGrouponUsers::where('user_id', $user->id)->where('multi_groupon_id', $multi_groupon_id)->where('order_id', $order->id)->first();

			return $this->success(['order' => $order, 'groupon_item_id' => $groupUser->multi_groupon_items_id]);
		} catch (\Exception $exception) {
			DB::rollBack();

			\Log::info($exception->getMessage());
			\Log::info($exception->getTraceAsString());

			return $this->failed($exception->getMessage());
		}
	}
}
