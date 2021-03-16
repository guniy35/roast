<?php

namespace GuoJiangClub\Discover\Core\Models;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use Illuminate\Database\Eloquent\Model;

class DiscoverContent extends Model
{
	protected $guarded = ['id'];

	protected $appends = ['nick_name', 'avatar', 'multiple_img_list'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'discover_content');
	}

	public function goods()
	{
		return $this->belongsTo(Goods::class, 'recommend_goods_id');
	}

	public function brand()
	{
		return $this->belongsTo(Brand::class, 'brand_id');
	}

	public function category()
	{
		return $this->belongsTo(DiscoverCategory::class, 'discover_category_id');
	}

	public function getNickNameAttribute()
	{
		if ($this->meta && !empty($meta = json_decode($this->meta, true))) {
			return $meta['nick_name'];
		}

		return '';
	}

	public function getAvatarAttribute()
	{
		if ($this->meta && !empty($meta = json_decode($this->meta, true))) {
			return $meta['avatar'];
		}

		return '';
	}

	public function setImgListAttribute($value)
	{
		if (is_array($value) && !empty($value)) {
			$this->attributes['img_list'] = json_encode($value);
		}
	}

	public function getImgListAttribute($value)
	{
		if ($value && is_string($value)) {
			return json_decode($value, true);
		}

		return $value;
	}

	public function getMultipleImgListAttribute()
	{
		return $this->img_list;
	}

	public function setTagsListAttribute($value)
	{
		if (is_array($value)) {
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
}