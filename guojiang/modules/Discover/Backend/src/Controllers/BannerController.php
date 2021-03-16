<?php

namespace GuoJiangClub\Discover\Backend\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use iBrand\Backend\Http\Controllers\ModelForm;
use GuoJiangClub\Discover\Core\Models\DiscoverBanner;
use GuoJiangClub\Discover\Core\Models\DiscoverCategory;
use GuoJiangClub\Discover\Core\Models\DiscoverTag;
use Illuminate\Routing\Controller;

class BannerController extends Controller
{
	use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {
			$content->description('轮播图列表');

			$content->breadcrumb(
				['text' => '轮播图列表', 'left-menu-active' => '轮播图']
			);

			$content->body($this->grid()->render());
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->description('添加轮播图');

			$content->breadcrumb(
				['text' => '添加轮播图', 'left-menu-active' => '轮播图']
			);

			$content->body($this->form()->render());
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->description('修改轮播图');

			$content->breadcrumb(
				['text' => '修改轮播图', 'left-menu-active' => '轮播图']
			);

			$content->body($this->form()->edit($id));
		});
	}

	public function form()
	{
		return Admin::form(DiscoverBanner::class, function (Form $form) {
			$form->image('img', 'banner图')->rules('required', ['img.required' => '请上传 banner图'])->uniqueName()->removable();
			$form->text('keywords', '搜索关键字')->rules('required', ['keywords.required' => '请填写 搜索关键字']);
			$form->select('brand_id', '所属品牌')->options(Brand::where('is_show', 1)->pluck('name', 'id'))->placeholder('请选择 所属品牌');
			$form->select('discover_category_id', '所属分类')->options(DiscoverCategory::where('status', 1)->pluck('name', 'id'))->placeholder('请选择 所属分类');
			$form->multipleSelect('tags_list', '标签')->options(DiscoverTag::where('status', 1)->pluck('name', 'id'))->placeholder('请选择 所属标签');
			$form->radio('status', '状态')->default(1)->options([1 => '发布', 0 => '下架']);

			$form->saving(function (Form $form) {
				if (!$form->brand_id) {
					$form->brand_id = 0;
				}

				if (!$form->discover_category_id) {
					$form->discover_category_id = 0;
				}
			});

			$form->saved(function (Form $form) {
				$img = $form->model()->img;
				if ($img && !str_contains($img, config('ibrand.backend.disks.admin.url'))) {
					$form->model()->img = config('ibrand.backend.disks.admin.url') . '/' . $img;
				}

				$form->model()->save();
			});

			$form->tools(function (Form\Tools $tools) {
				$tools->disableDelete();
				$tools->disableView();
			});

			$form->footer(function (Form\Footer $footer) {
				$footer->disableReset();
			});
		});
	}

	public function grid()
	{
		return Admin::grid(DiscoverBanner::class, function (Grid $grid) {
			$grid->id()->sortable();
			$grid->column('img', 'banner图')->display(function ($img) {
				return '<img width="80" src="' . $img . '">';
			});
			$grid->column('keywords', '搜索关键字');
			$grid->column('tags_list', '标签')->display(function ($tags) {
				if ($tags) {
					$tags = DiscoverTag::whereIn('id', $tags)->get();

					return $tags->map(function ($tag) {
						return "<div style='margin-bottom: 5px;'><span class='label label-primary'>{$tag->name}</span></div>";
					})->implode('');
				}
			});

			$grid->column('brand_id', '所属品牌')->display(function () {
				if (!is_null($this->brand)) {
					return $this->brand->name;
				}

				return '';
			});

			$grid->column('discover_category_id', '所属分类')->display(function () {
				if (!is_null($this->category)) {
					return $this->category->name;
				}

				return '';
			});
			$grid->column('status', '状态')->display(function ($status) {
				switch ($status) {
					case 0:
						$statusText = '下架';
						break;
					case 1:
						$statusText = '发布';
						break;
				}

				return $statusText;
			});
			$grid->column('created_at', '添加时间');
			$grid->disableExport();
			$grid->actions(function ($actions) {
				$actions->disableView();
			});

			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('keywords', '搜索关键字');
				$filter->like('tags_list', '所属标签')->select(DiscoverTag::where('status', 1)->pluck('name', 'id'));
				$filter->equal('brand_id', '所属品牌')->select(Brand::where('is_show', 1)->pluck('name', 'id'));
				$filter->equal('discover_category_id', '所属分类')->select(DiscoverCategory::where('status', 1)->pluck('name', 'id'));
				$filter->equal('status', '状态')->radio([1 => '发布', 0 => '下架']);
			});
		});
	}
}