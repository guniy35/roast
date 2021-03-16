<?php

namespace GuoJiangClub\Discover\Core\Models;

use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use Illuminate\Database\Eloquent\Model;

class DiscoverBanner extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'discover_banner');
	}

	public function setTagsListAttribute($value)
	{
		if (is_array($value) && !empty($value)) {
			$this->attributes['tags_list'] = implode(',', $value);
		}
	}

	public function getTagsListAttribute($value)
	{
		if ($value && is_string($value)) {
			return array_filter(explode(',', $value));
		}

		return $value;
	}

	public function brand()
	{
		return $this->belongsTo(Brand::class, 'brand_id');
	}

	public function category()
	{
		return $this->belongsTo(DiscoverCategory::class, 'discover_category_id');
	}
}