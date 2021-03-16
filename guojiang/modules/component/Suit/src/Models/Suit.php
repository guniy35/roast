<?php

namespace GuoJiangClub\Component\Suit\Models;

use Illuminate\Database\Eloquent\Model;

class Suit extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'suit');
	}

	public function items()
	{
		return $this->hasMany(SuitItems::class, 'suit_id');
	}

	public function setFirstNameAttribute($value)
	{
		$this->attributes['first_name'] = strtolower($value);
	}

}

