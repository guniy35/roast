<?php

namespace GuoJiangClub\Discover\Server\Controllers;

use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Distribution\Core\Models\Agent;
use iBrand\Common\Controllers\Controller;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;
use Illuminate\Support\Facades\Storage;

class GoodsController extends Controller
{
	protected $goodsRepository;

	public function __construct(GoodsRepository $goodsRepository)
	{
		$this->goodsRepository = $goodsRepository;
	}

	public function list()
	{
		$list = $this->goodsRepository->scopeQuery(function ($query) {
			return $query->where('is_del', 0)->where('is_first_order_free', 1);
		})->orderBy('sort', 'desc')->all();

		return $this->success($list);
	}

	public function createFreeGoodsShareImage()
	{
		$user = auth('api')->user();
		$page = request('pages');

		$user_id = 0;
		$scene   = '';
		if ($user) {
			$user_id = $user->id;
			//参数说明：1 用户ID 2 分销员code
			//$scene =  $user_id ;
			if ($agent = Agent::where(['user_id' => $user_id, 'status' => 1])->first()) {
				$scene = $agent->code;
			}
		}

		$app_id    = config('ibrand.wechat.mini_program.default.app_id');
		$img_name  = $scene . '_' . 'share' . '_' . $app_id . '_free_goods_mini_qrcode.jpg';
		$save_path = 'share/mini/qrcode/' . $img_name;
		$exists    = Storage::disk('public')->exists($save_path);
		if (!$exists) {
			platform_application()->createMiniQrcode($app_id, $page, 160, $scene);
		}
		/*dd($save_path);

		if (!Storage::disk('public')->exists($save_path)) {
			return $this->failed( '生成小程序码失败');
		}*/

		$route = url('api/free/goods/share/image/template?user_id=' . $user_id);

		$data = MiniProgramShareImg::generateShareImage($route);
		if ($data) {
			return $this->success(['image' => $data['url']]);
		}

		return $this->failed('生成失败');
	}

	public function freeGoodsShareImageTemplate()
	{
		$user_id   = 0;
		$scene     = '';
		$user_name = '';
		$avatar    = '';
		if (request('user_id') && request('user_id') > 0) {
			$user    = User::find(request('user_id'));
			$user_id = $user->id;
			//参数说明：1 商品ID，2 分销员code，3 grouponitemid 4 用户ID，用于分享者获得积分
			//$scene =  $user->id ;
			if ($agent = Agent::where(['user_id' => $user->id, 'status' => 1])->first()) {
				$scene = $agent->code;
			}
			$user_name = $user->nick_name;
			$avatar    = $user->avatar;
		}

		$app_id     = config('ibrand.wechat.mini_program.default.app_id');
		$img_name   = $scene . '_' . 'share' . '_' . $app_id . '_mini_qrcode.jpg';
		$save_path  = 'share/mini/qrcode/' . $img_name;
		$mini_image = Storage::disk('public')->url($save_path);

		return view('server::share.first_order', compact('user_id', 'user_name', 'avatar', 'mini_image'));
	}

	public function vipGoodsList()
	{
		$where['is_virtual'] = 1;
		$where['is_del']     = 0;

		$goods = $this->goodsRepository->scopeQuery(function ($query) use ($where) {
			if (!empty($where)) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			return $query;
		})->all();

		foreach ($goods as $item) {
			$meta = [];
			if ($item->products()->count() > 0) {
				$product              = $item->products()->first();
				$meta['sku']          = $product->sku;
				$meta['product_id']   = $product->id;
				$meta['store_count']  = $product->store_nums;
				$meta['price']        = $product->sell_price;
				$meta['market_price'] = $product->market_price;
				$meta['img']          = $product->photo_url;
				$specsText            = explode(' ', $product->SpecsText);
				$meta['color']        = isset($specsText[0]) ? $specsText[0] : '';
				$meta['size']         = isset($specsText[1]) ? $specsText[1] : '';
			} else {
				$meta['product_id']   = $item->id;
				$meta['store_count']  = $item->store_nums;
				$meta['price']        = $item->sell_price;
				$meta['market_price'] = $item->market_price;
				$meta['img']          = $item->img;
			}

			$item->meta = $meta;
		}

		return $this->success($goods->toArray());
	}
}