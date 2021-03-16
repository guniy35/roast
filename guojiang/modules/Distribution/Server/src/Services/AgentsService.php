<?php

namespace GuoJiangClub\Distribution\Server\Services;

use DB;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\Distribution\Server\Repository\AgentGoodsRepository;
use GuoJiangClub\EC\Open\Core\Common\MiniProgram as MiniGetWxaCode;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AgentsService
{
	protected $miniQrcode;
	protected $agentGoodsRepository;

	function __construct(AgentGoodsRepository $agentGoodsRepository, MiniGetWxaCode $miniGetWxaCode)
	{
		$this->miniQrcode           = $miniGetWxaCode;
		$this->agentGoodsRepository = $agentGoodsRepository;
	}

	public function countUserByDate($agent_id, $date)
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		return DB::table($prefix . 'agent_user_relation')->where('agent_id', $agent_id)->whereBetween('created_at', $date)->count();
	}

	public function countAgentOrderByDate($agent_id, $date)
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		return DB::table($prefix . 'agent_order')->where('agent_id', $agent_id)->whereBetween('created_at', $date)->count();
	}

	public function countAgentOrderTotalByDate($agent_id, $date, $order_status)
	{
		$prefix     = config('ibrand.app.database.prefix', 'ibrand_');
		$orderTable = config('ibrand.app.database.prefix', 'ibrand_') . 'order';

		return DB::table($prefix . 'agent_order')->where($prefix . 'agent_order.agent_id', $agent_id)->whereIn($prefix . 'agent_order.status', [0, 1])->whereBetween($prefix . 'agent_order.created_at', $date)->join($orderTable, function ($join) use ($order_status, $orderTable, $prefix) {
			$join->on($prefix . 'agent_order.order_id', '=', $orderTable . '.id')->whereIn($orderTable . '.status', $order_status);
		})->select($orderTable . '.total')->sum('total');
	}

	public function createMiniGoodsShareImg($goods, $user)
	{
		$checkGoodsMiniPath = $goods->id . '_mini.jpg';
		$miniPath           = 'agent/share/goods/' . $goods->id . '_' . time() . '_share_mini.jpg';
		$scene              = $goods->id;
		/*Storage::makeDirectory('public/agent/share/goods');*/
		Storage::makeDirectory('public/agent/goods_mini');
		Storage::makeDirectory('public/agent/share/goods/' . $goods->id);

		$icon_px = 36;  //顶部icon X轴位置
		if ($user) {
			$scene = $goods->id . ',,,' . $user->id;  //参数说明：1 商品ID，2 分销员code，3 grouponitemid 4 用户ID，用于分享者获得积分
			Storage::makeDirectory('public/agent/' . $user->id . '/share/goods');
			$checkGoodsMiniPath = $user->id . '/' . $goods->id . '_mini.jpg';
			$miniPath           = 'agent/share/goods/' . $user->id . '/' . $goods->id . '_' . time() . '_share_mini.jpg';
			Storage::makeDirectory('public/agent/share/goods/' . $user->id);
			if ($agent = Agent::where(['user_id' => $user->id, 'status' => 1])->first()) {
				$scene = $goods->id . ',' . $agent->code . ',,' . $user->id;
			}
			// $icon_px = 516;
		}

		$exists = Storage::disk('public')->exists($miniPath);
		if ($exists) {  //判断商品分享的海报是否存在
			return env('APP_URL') . '/storage/' . $miniPath;
		}

		$goodsImg = Image::make($goods->img);
		$goodsImg->resize(728, null, function ($constraint) {
			$constraint->aspectRatio();
		});

		$goodsImg->save(storage_path('app/public/agent/goods_mini/' . $goods->id . '_728.jpg'));
		$goodsImg = storage_path('app/public/agent/goods_mini/' . $goods->id . '_728.jpg');

		$img = Image::make(public_path('assets/backend/distribution/mini_goods_share_bg.jpg'));

		$icon_tip = config('store.goods_share_top_tips') ? config('store.goods_share_top_tips') : '力推时尚全能好货';

		$img->insert(public_path('assets/backend/distribution/mini_goods_share_icon.jpg'), 'top-left', $icon_px, 53); //添加顶部icon
		$img->text($icon_tip, $icon_px + 38, 73, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(26);
			$font->color('#595a5a');
		});

		$img->insert($goodsImg, 'top-left', 36, 106); //添加商品图片

		//添加文案
		$tips = config('store.goods_share_bottom_tips') ? config('store.goods_share_bottom_tips') : '米尔优选 为您提供全球好货';

		$tipsPix = imagettfbbox(26, 0, public_path('assets/backend/distribution/msyh.ttf'), $tips);

		$tipsWidth = abs($tipsPix[2] - $tipsPix[0]) - 90;  //预估存在90px的误差

		$img->text($tips, 400 - (int) abs($tipsWidth / 2), 875, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(26);
			$font->color('#ff2741');
		});

		//添加商品标题
		$title = $this->autoWrap(40, public_path('assets/backend/distribution/msyh.ttf'), $goods->name, 600);
		$img->text($title, 40, 970, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(32);
			$font->color('#000000');
		});

		//添加原价
		$market_price = '原价 ￥' . number_format($goods->market_price, 2);
		$img->text($market_price, 40, 1100, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(22);
			$font->color('#9B9B9B');
		});

		$b = imagettfbbox(32, 0, public_path('assets/backend/distribution/msyh.ttf'), $market_price);
		$w = abs($b[2] - $b[0]) - 130;
		$h = abs($b[5] - $b[3]);
		$img->line(40, 1100 - 5, 40 + $w, 1100 - 5, function ($draw) {
			$draw->color('#9B9B9B');
		});

		//添加销售价
		$sellPrice = $goods->sell_price;
		$price     = '￥' . number_format($sellPrice, 2);
		$img->text($price, 32, 1160, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(40);
			$font->color('#E73237');
		});
		/*$mini_image = storage_path('app/public/12478_mini_qrcode.jpg');*/
		$exists = Storage::disk('public')->exists('agent/goods_mini/' . $checkGoodsMiniPath);
		if ($exists) {  //判断商品的太阳码是否存在
			$mini_image = env('APP_URL') . '/storage/agent/goods_mini/' . $checkGoodsMiniPath;
		} else {
			$save_path = 'public/agent/goods_mini/' . $checkGoodsMiniPath;
			$mini      = $this->miniQrcode->createMiniQrcode('pages/store/detail/detail', 160, $save_path, 'B', $scene);
			if ($mini) {
				$mini_image = env('APP_URL') . '/storage/agent/goods_mini/' . $checkGoodsMiniPath;
				$mini_image = Image::make($mini_image);
				$mini_image->resize(230, 230);
				$mini_image->save(storage_path('app/' . $save_path));
			} else {
				return false;
			}
		}
		$img->insert($mini_image, 'top-left', 520, 930); //添加小程序图片

		$img->text('扫码或长按 识别小程序码', 520, 1180, function ($font) {
			$font->file(public_path('assets/backend/distribution/msyh.ttf'));
			$font->size(18);
			$font->color('#343333');
		});

		$img->save(storage_path('app/public/' . $miniPath));

		return env('APP_URL') . '/storage/' . $miniPath;
	}

	/**
	 * 根据预设宽度让文字自动换行
	 *
	 * @param int    $fontsize  字体大小
	 * @param string $ttfpath   字体名称
	 * @param string $str       字符串
	 * @param int    $width     预设宽度
	 * @param int    $fontangle 角度
	 * @param string $charset   编码
	 *
	 * @return string $_string  字符串
	 */
	protected function autoWrap($fontsize, $ttfpath, $str, $width, $fontangle = 0, $charset = 'utf-8')
	{
		$_string = "";
		$_width  = 0;
		$temp    = self::chararray($str, $charset);
		foreach ($temp[0] as $v) {
			$w      = self::charWidth($fontsize, $fontangle, $v, $ttfpath);
			$_width += intval($w);
			if (($_width > $width) && ($v !== "")) {
				$_string .= PHP_EOL;
				$_width  = 0;
			}
			$_string .= $v;
		}

		return $_string;
	}

	/**
	 * 返回一个字符的数组
	 *
	 * @param string $str     文字
	 * @param string $charset 字符编码
	 *
	 * @return array $match   返回一个字符的数组
	 */
	protected static function charArray($str, $charset = "utf-8")
	{
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);

		return $match;
	}

	/**
	 * 返回一个字符串在图片中所占的宽度
	 *
	 * @param int    $fontsize  字体大小
	 * @param int    $fontangle 角度
	 * @param string $ttfpath   字体文件
	 * @param string $char      字符
	 *
	 * @return int $width
	 */
	protected static function charWidth($fontsize, $fontangle, $char, $ttfpath)
	{
		$box   = @imagettfbbox($fontsize, $fontangle, $ttfpath, $char);
		$width = max($box[2], $box[4]) - min($box[0], $box[6]);

		return $width;
	}

	public function getCommissionByGoodsID($goodsInfo)
	{
		$agentGoods = $this->agentGoodsRepository->findWhere(['goods_id' => $goodsInfo->id])->first();
		$rate       = settings('distribution_rate');
		$commission = 0;

		if ($agentGoods AND $agentGoods->activity == 1) {
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

		return $commission;
	}

}