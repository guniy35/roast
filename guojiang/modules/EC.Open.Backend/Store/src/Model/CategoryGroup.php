<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryGroup extends Model
{
	use SoftDeletes;

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'category_group');
	}
}