<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Http\Controllers;

use iBrand\Backend\Http\Controllers\Controller;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ArticleRepository;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Service\GoodsService;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use GuoJiangClub\EC\Open\Backend\Store\Model\Article;
use Illuminate\Http\Request;
use Validator;

class ArticleController extends Controller
{
	protected $articleRepository;
	protected $goodsRepository;
	protected $goodsService;

	public function __construct(ArticleRepository $articleRepository,
	                            GoodsRepository $goodsRepository,
	                            GoodsService $goodsService)
	{
		$this->articleRepository = $articleRepository;
		$this->goodsRepository   = $goodsRepository;
		$this->goodsService      = $goodsService;
	}

	public function index()
	{
		$where = [];

		$articles = $this->articleRepository->getArticlePaginate($where);

		return Admin::content(function (Content $content) use ($articles) {

			$content->description('文章列表');

			$content->breadcrumb(
				['text' => '文章列表', 'left-menu-active' => '文章管理']
			);

			$content->body(view('store-backend::article.index', compact('articles')));
		});
	}

	public function create()
	{
		return Admin::content(function (Content $content) {
			$content->description('添加文章');

			$content->breadcrumb(
				['text' => '添加文章', 'left-menu-active' => '文章管理']
			);

			$content->body(view('store-backend::article.create'));
		});
	}

	public function edit($id)
	{
		$article = $this->articleRepository->find($id);

		return Admin::content(function (Content $content) use ($article) {
			$content->description('添加文章');

			$content->breadcrumb(
				['text' => '添加文章', 'left-menu-active' => '文章管理']
			);

			$content->body(view('store-backend::article.edit', compact('article')));
		});
	}

	public function getSpu(Request $request)
	{
		$url    = 'admin.bai.jia.article.getSpuData';
		$action = $request->input('action');
		$id     = $request->input('id');
		if ($id) {
			$goods = $this->articleRepository->find($id, ['goods']);
		}

		return view('store-backend::article.modal.getSpu', compact('goods', 'url', 'action'));
	}

	public function getSpuData(Request $request)
	{
		$ids                 = explode(',', $request->input('ids'));
		$action              = $request->input('action');
		$_where              = [];
		$where['is_del']     = 0;
		$where['store_nums'] = ['>', 0];

		if (!empty(request('value')) and 'sku' !== request('field') and 'category' !== request('field')) {
			$where[request('field')] = ['like', '%' . request('value') . '%'];
		}

		$goods_ids = [];
		if ('sku' == request('field') && !empty(request('value'))) {
			$goods_ids = $this->goodsService->skuGetGoodsIds(request('value'));
		}
		if ('category' == request('field') && !empty(request('value'))) {
			$goods_ids = $this->goodsService->categoryGetGoodsIds(request('value'));
		}

		if ('view' == $action or 'view_exclude' == $action) {
			$goods_ids = array_merge($goods_ids, $ids);
		}

		$goods        = $this->goodsRepository->getGoodsPaginated($where, $_where, $goods_ids, 15)->toArray();
		$goods['ids'] = $ids;

		return $this->ajaxJson(true, $goods);
	}

	public function store(Request $request)
	{
		$input      = $request->except('_token', 'file');
		$rules      = [
			'title'          => 'required',
			'img'            => 'required',
			'article_detail' => 'required',
			'goods'          => 'required',
		];
		$messages   = [
			'required' => ':attribute 不能为空',
		];
		$attributes = [
			'title'          => '文章标题',
			'img'            => '展示图片',
			'article_detail' => '文章详情',
			'goods'          => '关联商品',
		];

		if ($input['type'] == Article::TYPE_STARS_RECOMMEND) {
			$rules = array_merge($rules, [
				'author'        => 'required',
				'author_title'  => 'required',
				'author_avatar' => 'required',
			]);

			$attributes = array_merge($attributes, [
				'author'        => '文章发布人',
				'author_title'  => '发布人头衔',
				'author_avatar' => '发布人头像',
			]);
		}

		$validator = Validator::make($input, $rules, $messages, $attributes);
		if ($validator->fails()) {
			$message = $validator->messages()->first();

			return $this->ajaxJson(false, [], 500, $message);
		}

		if (isset($input['id']) && $input['id']) {
			$this->articleRepository->update($input, $input['id']);
		} else {
			$this->articleRepository->create($input);
		}

		return $this->ajaxJson();
	}

	public function delete($id)
	{
		$article = $this->articleRepository->find($id);
		$article->delete();

		return $this->ajaxJson();
	}

	public function status()
	{
		$article = $this->articleRepository->findWhere(['id' => request('modelId')])->first();

		$article->status = request('status');
		$article->save();

		return $this->ajaxJson();
	}
}
