<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;

class MerchantPay extends Model
{

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'merchant_pay');
	}

	public function multiGrouponUser()
	{
		return $this->belongsTo(MultiGrouponUsers::class, 'origin_id');
	}
}