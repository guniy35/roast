<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product as GoodsProduct;

class GoodsLimit extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'goods_limit');
	}

	public function goods()
	{
		return $this->belongsTo(Goods::class);
	}

	public function product()
	{
		return $this->hasMany(GoodsProduct::class, 'goods_id', 'goods_id');
	}
}