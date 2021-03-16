<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\EC\Open\Backend\Store\Repositories\GoodsRepository;
use GuoJiangClub\EC\Open\Backend\Store\Model\Article;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ArticleRepository;
use GuoJiangClub\EC\Open\Server\Transformers\ArticleTransformer;

class ArticleController extends Controller
{
	protected $articleRepository;
	protected $goodsRepository;

	public function __construct(ArticleRepository $articleRepository, GoodsRepository $goodsRepository)
	{
		$this->articleRepository = $articleRepository;
		$this->goodsRepository   = $goodsRepository;
	}

	public function list()
	{
		$type  = request('type') ? request('type') : Article::TYPE_STARS_RECOMMEND;
		$limit = request('limit') ? request('limit') : 15;

		$where['type']   = $type;
		$where['status'] = 1;

		$list = $this->articleRepository->getArticlePaginate($where, $limit);

		$meta['img_list'] = [];
		$recommend        = $this->articleRepository->findWhere(['type' => $type, 'status' => 1, 'is_recommend' => 1], ['id', 'img']);
		if ($recommend->count() > 0) {
			$meta['img_list'] = $recommend->toArray();
		}

		$meta['title'] = $type == Article::TYPE_STARS_RECOMMEND ? '明星大咖推荐' : '专属方案';

		return $this->response()->paginator($list, new ArticleTransformer())->setMeta($meta);
	}

	public function detail($id)
	{
		$article = $this->articleRepository->findWhere(['id' => $id, 'status' => Article::STATUS_VALID])->first();
		if (!$article) {
			return $this->failed('文章不存在或已下架');
		}

		$goods_list = [];
		$goods_tag  = [];
		preg_match_all('/goods_[\d]+/', $article->article_detail, $goods_tag);
		if (!empty($goods_tag[0])) {
			foreach ($goods_tag[0] as $item) {
				$tmp   = explode('_', $item);
				$goods = $this->goodsRepository->findWhere(['id' => $tmp[1]])->first();
				if ($goods) {
					$goods_list[] = $goods;
				}
			}
		}

		return $this->success(['article' => $article, 'goods_list' => $goods_list]);
	}
}