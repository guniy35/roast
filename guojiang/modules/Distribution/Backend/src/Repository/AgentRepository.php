<?php

namespace GuoJiangClub\Distribution\Backend\Repository;

use GuoJiangClub\Distribution\Backend\Models\Agent;
use Prettus\Repository\Eloquent\BaseRepository;

class AgentRepository extends BaseRepository
{
	public function model()
	{
		return Agent::class;
	}

	public function getAgentPaginate($where, $time, $limit)
	{
		return $this->scopeQuery(function ($query) use ($where, $time) {
			if (is_array($where) AND count($where) > 0) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						if ($key == 'filter') {
							$query = $query->where(function ($query) use ($operate, $va) {
								$query->where('name', $operate, $va)
									->orWhere('mobile', $operate, $va);
							});
						} else {
							$query = $query->where('name', $operate, $va);
						}
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			/*注册时间*/
			if (is_array($time) AND isset($time['created_at'])) {
				$query = $query->whereBetween('created_at', $time['created_at']);
			}

			return $query->orderBy('created_at', 'desc');
		})->paginate($limit);
	}

	public function formatToExcelData($agents)
	{
		$data = [];
		if ($agents AND count($agents) > 0) {
			$i = 0;
			foreach ($agents as $item) {
				$data[$i][] = $item->name;
				$data[$i][] = $item->mobile;
				$data[$i][] = $item->manyUsers()->count();
				$data[$i][] = $item->orders()->count();
				$data[$i][] = $item->calculateCash();
				$data[$i][] = $item->calculateCommission(0);
				$data[$i][] = $item->created_at;

				$status = '';
				switch ($item->status) {
					case 0:
						$status = '待审核';
						break;
					case 1:
						$status = '已审核';
						break;
					case 2:
						$status = '审核未通过';
						break;
					case 3:
						$status = '已清退';
						break;
				}
				$data[$i][] = $status;

				$i++;
			}
		}

		return $data;
	}

	public function getAgentCountByDate($time)
	{
		return $this->scopeQuery(function ($query) use ($time) {
			return $query->whereBetween('created_at', $time);
		})->all()->count();
	}
}