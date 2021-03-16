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
use GuoJiangClub\Component\Advert\Models\MicroPage;
use GuoJiangClub\Component\Advert\Models\MicroPageAdvert;
use GuoJiangClub\Component\Advert\Repositories\AdvertItemRepository;
use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use GuoJiangClub\Component\Gift\Repositories\GiftActivityRepository;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\Gift\Models\GiftActivity;
use GuoJiangClub\Component\Gift\Repositories\CardRepository;
use DB;

class MicroPageController extends Controller
{
	protected $microPage;
	protected $microPageAdvert;
	protected $advertItemRepository;
	protected $giftActivityRepository;
	protected $pointRepository;
	protected $cardRepository;

	public function __construct(MicroPage $microPage,
	                            microPageAdvert $microPageAdvert,
	                            AdvertItemRepository $advertItemRepository,
	                            GoodsRepository $goodsRepository,
	                            GiftActivityRepository $giftActivityRepository,
	                            PointRepository $pointRepository,
	                            CardRepository $cardRepository)
	{
		$this->microPage              = $microPage;
		$this->microPageAdvert        = $microPageAdvert;
		$this->advertItemRepository   = $advertItemRepository;
		$this->goodsRepository        = $goodsRepository;
		$this->giftActivityRepository = $giftActivityRepository;
		$this->pointRepository        = $pointRepository;
		$this->cardRepository         = $cardRepository;
	}

	public function index($code)
	{
		$data['pages'] = null;
		switch ($code) {
			case is_numeric($code):
				$microPage = $this->microPage->where('id', $code)->first();
				break;
			case 'index':
				$microPage = $this->microPage->where('page_type', MicroPage::PAGE_TYPE_HOME)->first();
				break;
			default:
				$microPage = $this->microPage->where('code', $code)->first();
		}

		if (request('name')) {
			$microPage = $this->microPage->where('name', request('name'))->first();
		}

		if (!$microPage) {
			return $this->success();
		}

		$microPageAdverts = $this->microPageAdvert->where('micro_page_id', $microPage->id)
			->with(['advert' => function ($query) {
				return $query = $query->where('status', 1);
			}])
			->orderBy('sort')->get();

		if ($microPageAdverts->count()) {
			$i = 0;

			foreach ($microPageAdverts as $key => $item) {
				$associate_with = [];

				if ($item->advert_id > 0) {
					$data['pages'][$i]['name'] = $item->advert->type;

					$data['pages'][$i]['title'] = $item->advert->title;

					$data['pages'][$i]['is_show_title'] = $item->advert->is_show_title;

					$data['pages'][$i]['meta'] = $item->meta ? json_decode($item->meta, true) : null;

					if ($item->advert->type == 'micro_page_componet_suit') {

						$associate_with = ['items', 'items.goods'];
					}

					if ($item->advert->type == 'micro_page_componet_seckill') {

						$associate_with = ['goods', 'seckill'];
					}

					if ($item->advert->type == 'micro_page_componet_groupon'
						|| $item->advert->type == 'micro_page_componet_free_event') {

						$associate_with = ['goods'];
					}

					if ('micro_page_componet_groupon' == $item->advert->type) {
						$associate_with = ['goods'];
					}

					if (stristr($item->advert->type, 'componet_cube')) {
						$data['pages'][$i]['name'] = 'micro_page_componet_cube';

						$cube_type = '1_1';

						$arr = explode('_', $item->advert->type);

						$len = count($arr);

						if (is_numeric($arr[$len - 1])) {
							$cube_type = $arr[$len - 2] . '_' . $arr[$len - 1];
						}

						$data['pages'][$i]['type'] = $cube_type;
					}

					$advertItem = $this->getAdvertItem($item->advert->code, $associate_with);

					$data['pages'][$i]['value'] = array_values($advertItem);
				}

				if (-1 == $item->advert_id) {
					$data['pages'][$i]['name'] = 'micro_page_componet_search';

					$data['pages'][$i]['value'] = null;
				}

				++$i;
			}
		}

		$data['server_time'] = Carbon::now()->toDateTimeString();

		$data['micro_page'] = $microPage;
		if (!empty($microPage->meta)) {
			$microPage->meta = json_decode($microPage->meta, true);
		}

		return $this->success($data);
	}

	public function getAdvertItem($code, $associate_with)
	{
		$advertItem = $this->advertItemRepository->getItemsByCode($code, $associate_with);

		$time = Carbon::now()->toDateTimeString();

		if ($advertItem->count()) {
			$filtered = $advertItem->filter(function ($item) use ($time) {
				if (!$item->associate and $item->associate_id) {
					return [];
				}

				switch ($item->associate_type) {
					case 'seckillItem':
					case 'suit':
					case 'groupon':
					case 'freeEvent':
					case 'discount':

						if (1 == $item->associate->status and $item->associate->ends_at > $time) {
							return $item;
						}

						break;

					case 'category':

						$prefix = config('ibrand.app.database.prefix', 'ibrand_');

						$category_id = $item->associate_id;

						$categoryGoodsIds = DB::table($prefix . 'goods_category')
							->where('category_id', $category_id)
							->select('goods_id')->distinct()->get()
							->pluck('goods_id')->toArray();

						$goodsList = DB::table($prefix . 'goods')
							->whereIn('id', $categoryGoodsIds)
							->where('is_del', 0)
//                            ->orderBy('sort', 'desc')
							->limit($item->meta['limit'])->get();

						$item->goodsList = $goodsList;

						return $item;

						break;

					case null:

						if ($item->children AND $item->children->count()) {

							foreach ($item->children as $citem) {

								if ($citem->associate_type == 'goods') {

									$citem->goods_id = $citem->associate_id;
								}
							}
						}

						return $item;

						break;

					default:

						return $item;
				}
			});

			return $filtered->all();
		}

		return $advertItem;
	}

	public function giftNewUser()
	{
		$gift_new_user = $this->giftActivityRepository->giftListEffective('gift_new_user');

		return $this->success($gift_new_user);
	}

	public function giftNewUserLanded()
	{
		$user = request()->user();
		if ($gift = $this->giftActivityRepository->DateProcessingGiftNewUser($user)) {
			$is_new_user = $gift->is_new_user;

			event('gift.new.user.point', [$user, $gift]);
			event('gift.new.user.coupon', [$user, $gift]);

			if (!$gift_new_user = $this->giftActivityRepository->DateProcessingGiftNewUser($user)) {
				return $this->success([]);
			}
			$point                       = $this->pointRepository->findWhere(['action'  => 'gift_new_user_point',
			                                                                  'item_id' => $gift_new_user->id, 'item_type' => GiftActivity::class, 'user_id' => $user->id])->first();
			$gift_new_user->point_status = $point ? true : false;
			$gift_new_user->is_new_user  = $is_new_user;
			$date['user']                = $user;
			$date['activity']            = $gift_new_user;

			return $this->success($date);
		}

		return $this->success([]);
	}

	public function giftBirthday()
	{
		$user = request()->user();
		$gift = $this->giftActivityRepository->DateProcessingGiftBirthday($user);
		if ($gift And isset($gift->activity_day)) {
			$users = $this->cardRepository->getInstantBirthdayUserByDay([], $gift->activity_day, 0, true);

			if (!$this->cardRepository->checkUserBirthdayInUsers($user->id, $users)) {
				return $this->success([]);
			}

			event('gift.birthday.point', [$user, $gift]);
			event('gift.birthday.coupon', [$user, $gift]);

			if (!$gift_new = $this->giftActivityRepository->DateProcessingGiftBirthday($user)) {
				return $this->success([]);
			}

			$point_status = false;
			$time         = Carbon::now()->timestamp;
			$birthday     = date('Y-m-d', $time);
			if ($point = $this->pointRepository->orderBy('created_at', 'desc')->findWhere(['action' => 'gift_birthday_point', 'user_id' => $user->id])->first()) {
				if (intval(strtotime(date('Y-m-d', strtotime($point->created_at))) == intval(strtotime($birthday)))) {
					$point_status = true;
				}
			}
			$gift_new->point_status = $point_status ? true : false;
			$date['user']           = $user;
			$date['activity']       = $gift_new;

			return $this->success($date);
		}

		return $this->success([]);
	}
}
