<?php

namespace GuoJiangClub\Component\Refund\Models;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use SoftDeletes;

    const STATUS_AUDIT  = 0;     //待审核
    const STATUS_PASS   = 1;      //审核通过
    const STATUS_REFUSE = 2;    //拒绝申请

    const STATUS_COMPLETE = 3;  //已完成
    const STATUS_CANCEL   = 4;    //已关闭

    const STATUS_USER_RETURN       = 5;   //等待用户退货
    const STATUS_USER_HAS_RETURNED = 6;     // 用户已退货或等待商家收货
    const STATUS_SHOP_SHIP         = 7;     //商家已发货

    const STATUS_SHOP_PAID = 8;    //等待商家退款

    const TYPE_REFUND = 1;    //  仅退款
    const TYPE_GOODS  = 4; //退货退款

    protected $guarded = ['id'];

    protected $appends = ['service_type_text'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->status    = self::STATUS_AUDIT;
        $this->refund_no = build_order_no('R');
        $this->setTable(config('ibrand.app.database.prefix', 'ibrand_').'refund');
    }

    public function logs()
    {
        return $this->hasMany(RefundLog::class);
    }

    public function setImagesAttribute($value)
    {
        if (empty($value)) {
            return;
        }
        $this->attributes['images'] = json_encode($value);
    }

    public function getImagesAttribute($value)
    {
        if (empty($value)) {
            return '';
        }

        return json_decode($value);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * 申请服务类型text
     */
    public function getServiceTypeTextAttribute()
    {
        if ($this->type == 1) {
            return '仅退款';
        } elseif ($this->type == 4) {
            return '退货退款';
        } elseif ($this->type == 2) {
            return '换货';
        } elseif ($this->order->distribution_status == 1 and $this->type == 1) {
            return '退货退款';
        } else {
            return '其他类型';
        }
    }

    public function getRefundTypeAttribute()
    {
        if (($this->order->distribution_status == 1 and $this->type == 1) or $this->type == 4) {
            return 'REFUND_GOODS';
        } elseif ($this->type == 1) {
            return 'REFUND';
        } elseif ($this->type == 2) {
            return 'GOODS';
        } else {
            return 'OTHER';
        }
    }

    public function shipping()
    {
        return $this->hasOne(RefundShipping::class);
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return '待审核';
                break;

            case 1:
                return '审核通过';
                break;

            case 2:
                return '拒绝申请';
                break;

            case 3:
                return '已完成';
                break;

            case 4:
                return '已关闭';
                break;

            case 5:
                return '等待用户退货';
                break;

            case 6:
                return '用户已退货';
                break;

            case 7:
                return '等待商城发货';
                break;

            case 8:
                return '等待商家退款';
                break;

            default:
                return '待审核';
        }
    }
}
