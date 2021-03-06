<?php

namespace GuoJiangClub\Discover\Core\Models;

use Illuminate\Database\Eloquent\Model;

class DiscoverCategory extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'discover_category');
	}
}