<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\Component\Seckill\Repositories\SeckillRepository;
use GuoJiangClub\Component\Seckill\Repositories\SeckillItemRepository;
use GuoJiangClub\EC\Open\Server\Transformers\SeckillItemTransformer;
use Illuminate\Pagination\LengthAwarePaginator;

class SeckillController extends Controller
{
	private $seckillRepository;
	private $seckillItemRepository;

	public function __construct(SeckillRepository $seckillRepository, SeckillItemRepository $seckillItemRepository)
	{
		$this->seckillRepository     = $seckillRepository;
		$this->seckillItemRepository = $seckillItemRepository;
	}

	/**获取秒杀活动列表
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function lists()
	{
		$limit = !empty(request('limit')) ? request('limit') : 15;

		$lists = $this->seckillItemRepository->getSeckillItemAll($limit);

		$data = $lists->sortBy('starts_at')->values();

		$lists = new LengthAwarePaginator($data, $lists->total(), $limit, $lists->currentPage());

		return $this->response()->paginator($lists, new SeckillItemTransformer());
	}

}