<?php

namespace GuoJiangClub\Discover\Backend\Controllers;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use iBrand\Backend\Http\Controllers\ModelForm;
use GuoJiangClub\Discover\Core\Models\DiscoverCategory;
use GuoJiangClub\Discover\Core\Models\DiscoverTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GuoJiangClub\Discover\Core\Models\DiscoverContent;
use Storage;

class ContentController extends Controller
{
	use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {
			$content->header('内容列表');
			$content->description('内容列表');

			$content->breadcrumb(
				['text' => '内容列表', 'left-menu-active' => '内容']
			);

			$content->body($this->grid()->render());
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->header('添加内容');
			$content->description('添加内容');

			$content->breadcrumb(
				['text' => '添加内容', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '内容']
			);

			$content->body($this->form()->render());
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->header('修改内容');
			$content->description('修改内容');

			$content->breadcrumb(
				['text' => '修改内容', 'url' => '', 'no-pjax' => 1, 'left-menu-active' => '内容']
			);

			$content->body($this->form()->edit($id));
		});
	}

	public function form()
	{
		return Admin::form(DiscoverContent::class, function (Form $form) {
			$form->display('nick_name', '昵称')->with(function () {
				return '米尔小助手';
			});
			$form->display('avatar', '头像')->with(function () {
				return '<img src="https://cdn.viperky.com/logo1.jpg" width="80">';
			});

			$form->textarea('description', '内容')->rules('required', ['description.required' => '请填写 内容']);
			$form->multipleImage('multiple_img_list', '展示图片')->removable()->uniqueName();
			$form->select('recommend_goods_id', '推荐商品')->options(Goods::where('is_del', 0)->pluck('name', 'id'));
			//->ajax('goods/list', 'id', 'name')->placeholder('请选择 推荐商品')
			$form->select('brand_id', '所属品牌')->options(Brand::where('is_show', 1)->pluck('name', 'id'))->placeholder('请选择 所属品牌');
			$form->select('discover_category_id', '分类')->options(DiscoverCategory::where('status', 1)->pluck('name', 'id'))->placeholder('请选择 所属分类');
			$form->multipleSelect('tags_list', '标签')->options(DiscoverTag::where('status', 1)->pluck('name', 'id'))->placeholder('请选择 所属标签');
			$form->radio('status', '状态')->default(1)->options([1 => '发布', 0 => '下架']);
			$form->radio('is_recommend', '是否推荐')->default(0)->options([1 => '是', 0 => '否']);
			$form->hidden('meta');

			$form->saving(function (Form $form) {
				if (!$form->recommend_goods_id) {
					$form->recommend_goods_id = 0;
				}

				if (!$form->brand_id) {
					$form->brand_id = 0;
				}

				if (!$form->discover_category_id) {
					$form->discover_category_id = 0;
				}

				$form->meta = json_encode(['nick_name' => '米尔小助手', 'avatar' => 'https://cdn.viperky.com/logo1.jpg']);

				if (request()->hasFile('multiple_img_list')) {
					$files    = request()->file('multiple_img_list');
					$tmp_path = [];
					foreach ($files as $file) {
						$name       = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
						$path       = $file->storeAs(config('app.name') . '/discover', $name, 'qiniu');
						$tmp_path[] = Storage::disk('qiniu')->url($path);
					}

					if (!empty($tmp_path)) {
						$form->model()->img_list = $tmp_path;
					}
				}
			});

			if (strtolower(request()->getMethod()) == 'post' || strtolower(request()->getMethod()) == 'put') {
				$form->ignore(['nick_name', 'avatar', 'multiple_img_list']);
			}

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
		return Admin::grid(DiscoverContent::class, function (Grid $grid) {
			$grid->id()->sortable();
			/*$grid->column('comment_user', "发布用户")->display(function () {
				return $this->nick_name . ' <img width="50" src="' . $this->avatar . '">';
			});*/
			$grid->column('description', "素材内容")->display(function () {
				$text = str_replace("\r\n", "<br />", $this->description) . '<br>';
				$img  = '';
				if ($this->img_list) {
					foreach ($this->img_list as $item) {
						$img = $img . '<img width="50" src="' . $item . '">';
					}
				}

				return $text . $img;
			});

			$grid->column('recommend_goods_id', "推荐商品")->display(function ($recommend_goods_id) {
				if ($recommend_goods_id) {
					$goods = Goods::find($recommend_goods_id);

					return $goods->name . ' <img width="50" src="' . $goods->img . '">';
				}
			});
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
			$grid->column('is_recommend', '是否推荐')->display(function ($is_recommend) {
				return $is_recommend == 1 ? '是' : '否';
			});
			$grid->column('created_at', '发布时间');
			$grid->disableExport();
			$grid->actions(function ($actions) {
				$actions->disableView();
			});

			$grid->filter(function ($filter) {
				$filter->disableIdFilter();
				$filter->like('tags_list', '所属标签')->select(DiscoverTag::where('status', 1)->pluck('name', 'id'));
				$filter->equal('brand_id', '所属品牌')->select(Brand::where('is_show', 1)->pluck('name', 'id'));
				$filter->equal('discover_category_id', '所属分类')->select(DiscoverCategory::where('status', 1)->pluck('name', 'id'));
				$filter->equal('status', '状态')->radio([1 => '发布', 0 => '下架']);
			});
		});
	}

	public function goodsList(Request $request)
	{
		$q = $request->get('q');

		return Goods::where('name', 'like', "%$q%")->orWhere('goods_no', 'like', "%$q%")->paginate(20, ['id', 'name']);
	}
}