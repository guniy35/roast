<?php

namespace GuoJiangClub\Component\Suit\Models;

use Illuminate\Database\Eloquent\Model;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product;

class SuitItems extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'suit_item');
	}

	public function goods()
	{
		return $this->hasOne(Goods::class, 'id', 'item_id');
	}

	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'item_id');
	}

	public function suit()
	{
		return $this->belongsTo(Suit::class, 'suit_id', 'id');
	}

	public function aa()
	{
		return $this->orderBy('sort', 'desc')->get();
	}

}

