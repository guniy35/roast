<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'shipping');
	}

	public function shippingMethod()
	{
		return $this->belongsTo('GuoJiangClub\EC\Open\Backend\Store\Model\ShippingMethod', 'method_id');
	}

}