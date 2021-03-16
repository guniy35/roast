<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use GuoJiangClub\Component\Order\Models\Adjustment;
use GuoJiangClub\Component\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuoJiangClub\EC\Open\Backend\Store\Model\Relations\BelongToUserTrait;

class Order extends Model implements Transformable
{
	use SoftDeletes;
	use TransformableTrait;
	use BelongToUserTrait;

	const STATUS_TEMP = 0;   //临时订单
	const STATUS_NEW  = 1;    //有效订单，待付款
	const STATUS_PAY  = 2;    //已支付订单，待发货

	const STATUS_DELIVERED = 3;    //已发货，待收货
	const STATUS_RECEIVED  = 4;    //已收货，待评价
	const STATUS_COMPLETE  = 5;    //已评价，订单完成

	const STATUS_PAY_PARTLY = 21;    //已经支付部分金额
	const STATUS_CANCEL     = 6; //已取消订单
	const STATUS_INVALID    = 8;//已作废订单
	const STATUS_REFUND     = 7;//有退款订单
	const STATUS_DELETED    = 9;//已删除订单

	const TYPE_DEFAULT   = 0;//默认类型
	const TYPE_DISCOUNT  = 1;//折扣订单
	const TYPE_IN_SOURCE = 2;//内购订单
	const TYPE_GIFT      = 3;//礼品订单
	const TYPE_SUIT      = 4;//套餐订单

	const TYPE_SHOP = 6;//门店O2O订单

	const TYPE_POINT   = 5;//积分商城订单   6是O2O订单
	const TYPE_SECKILL = 7;//秒杀订单

	/*distribution_status*/
	const DELIVERED_WAIT   = 0;  //待发货
	const DELIVERED_STATUS = 1; //已全部发货
	const DELIVERED_PARTLY = 2; //部分发货

	const TYPE_GROUPON               = 8;//拼团订单
	const TYPE_FREE_EVENT            = 9;//免费活动订单
	const TYPE_MULTI_GROUPON         = 10; //小拼团订单
	const TYPE_VIRTUAL_MULTI_GROUPON = 12; //小拼团虚拟订单

	const TYPE_ACTIVITY_TEMP = 110; //活动订单临时类型
	const TYPE_ACTIVITY      = 11; //活动订单

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'order');
	}

	public function items()
	{
		return $this->hasMany(OrderItem::class, 'order_id');
	}

	public function shipping()
	{
		return $this->hasMany(Shipping::class, 'order_id');
	}

	/**
	 * 订单状态
	 *
	 * @return string
	 */
	public function getStatusTextAttribute()
	{
		$text = '';

		switch ($this->status) {
			case 1:
				$text = "待付款";
				break;
			case 2:
				$text = "待发货";
				break;
			case 3:
				$text = "配送中";  //或 待收货
				break;
			case 4:
				$text = "待评价";  //已收货待评价
				break;
			case 5:
				$text = "已完成";
				break;
			case 6:
				$text = "已取消";
				break;
			case 7:
				$text = "退款中";
				break;
			case 8:
				$text = "已作废";
				break;
			case 9:
				$text = "已删除";
				break;
			default:
				$text = "待付款";
		}

		return $text;
	}

	public function getPayTypeTextAttribute()
	{
		$text = '';

		if (isset($this->payment->channel)) {
			switch ($this->payment->channel) {
				case 'alipay_wap':
					$text = "支付宝手机网页支付";
					break;
				case 'alipay_pc_direct':
					$text = "支付宝 PC 网页支付";
					break;
				case 'upacp_wap':
					$text = "银联支付";
					break;
				case 'wx_pub':
					$text = "微信支付";
					break;
				case 'wx_lite':
					$text = "小程序支付";
					break;
				case 'balance':
					$text = "余额支付";
					break;
				case 'wx_pub_qr':
					$text = "微信扫码支付";
					break;
				case 'test':
					$text = "测试";
			}
		}

		return $text;
	}

	public function getDistributionTextAttribute()
	{
		switch ($this->distribution_status) {
			case 0:
				return '未发货';
				break;
			case 1:
				return '已发货';
				break;
		}

		return '';
	}

	public function getPayStatusTextAttribute()
	{
		return $this->pay_status == 0 ? '未支付' : '已支付';
	}

	public function getItemsTotalAttribute($value)
	{
		return $value / 100;
	}

	public function getTotalAttribute($value)
	{
		return $value / 100;
	}

	public function getAdjustmentsTotalAttribute($value)
	{
		return $value / 100;
	}

	public function getRealAmountAttribute()
	{
		return $this->total + $this->adjustments_total;
	}

	public function getPayableFreightAttribute($value)
	{
		return $value / 100;
	}

	public function getRealFreightAttribute($value)
	{
		return $value / 100;
	}

	public function comments()
	{
		return $this->hasMany(OrderComment::class, 'order_id');
	}

	public function payment()
	{
		return $this->hasOne(\GuoJiangClub\EC\Open\Backend\Store\Model\Payment::class, 'order_id');
	}

	public function adjustments()
	{
		return $this->hasMany(Adjustment::class, 'order_id');
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function getAdjustments()
	{
		return $this->adjustments;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function countItems()
	{
		return $this->items->count();
	}

	public function getOrderTypeAttribute()
	{
		switch ($this->type) {
			case 0:
				return '普通订单';
				break;
			case 1:
				return '折扣订单';
				break;
		}

		return '普通订单';
	}

	public function getBalancePaidAttribute()
	{
		$amount = 0;
		if ($this->payment->count() > 0) {
			foreach ($this->payments as $item) {
				if ($item->status == Payment::STATUS_COMPLETED AND $item->channel == 'balance') {
					$amount += $item->amount;
				}
			}
		}

		return $amount;
	}

	public function getOrderUserNameAttribute()
	{
		$user = $this->user;
		if ($user) {
			if ($user->name) {
				return $user->name;
			}
			if ($user->mobile) {
				return $user->mobile;
			}
			if ($user->nick_name) {
				return $user->nick_name;
			}
		}

		return '/';
	}

	/**
	 * 获取tab订单数量
	 *
	 * @param       $status
	 * @param array $supplierID
	 *
	 * @return mixed
	 */
	public static function getOrdersCountByStatus($status)
	{
		$model = new self();
		if (is_array($status)) {
			return $model->whereBetween('status', $status)->whereHas('items', function ($query) {

			})->count();
		} else {
			return $model->where('status', $status)->whereHas('items', function ($query) use ($status) {

			})->count();
		}
	}

	public function getMobileAttribute($value)
	{
		return substr_replace($value, '****', 3, 5);
	}

	public function refunds()
	{
		return $this->hasMany(Refund::class);
	}

	public function getRefundStatusAttribute()
	{
		$refund = $this->refunds;
		if ($refund->count() > 0) {
			$filtered = $refund->filter(function ($value, $key) {
				return $value->status <> 3;
			});

			if ($filtered->count() > 0) {
				return $filtered->first()->StatusText;
			}

			return '已完成';
		}

		return '';
	}
}
