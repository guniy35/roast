<?php

namespace GuoJiangClub\Discover\Backend\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use iBrand\Backend\Http\Controllers\Controller;
use iBrand\Backend\Http\Controllers\ModelForm;
use GuoJiangClub\Discover\Core\Models\DiscoverCategory;

class CategoryController extends Controller
{
	use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {
			$content->description('分类列表');

			$content->breadcrumb(
				['text' => '分类列表', 'left-menu-active' => '分类']
			);

			$content->body($this->grid()->render());
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->description('添加分类');

			$content->breadcrumb(
				['text' => '添加分类', 'left-menu-active' => '分类']
			);

			$content->body($this->form()->render());
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->description('修改分类');

			$content->breadcrumb(
				['text' => '修改分类', 'left-menu-active' => '分类']
			);

			$content->body($this->form()->edit($id));
		});
	}

	public function form()
	{
		return Admin::form(DiscoverCategory::class, function (Form $form) {
			$form->text('name', '分类名称')->rules('required', ['name.required' => '请填写 分类名称']);
			$form->radio('status', '状态')->options([1 => '启用', 0 => '禁用'])->default(1);

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
		return Admin::grid(DiscoverCategory::class, function (Grid $grid) {
			$grid->id()->sortable();
			$grid->column('name', '分类名称');
			$grid->column('status', '状态')->display(function ($status) {
				return $status ? '启用' : '禁用';
			});

			$grid->disableExport();
			$grid->actions(function ($actions) {
				$actions->disableView();
			});

			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('name', '分类名称');
				$filter->equal('status', '状态')->radio([1 => '启用', 0 => '禁用']);
			});
		});
	}
}