<?php

namespace GuoJiangClub\Distribution\Backend\Repository;

use GuoJiangClub\Distribution\Backend\Models\BalanceCash;
use Prettus\Repository\Eloquent\BaseRepository;

class CashRepository extends BaseRepository
{
	public function model()
	{
		return BalanceCash::class;
	}

	public function getCashPaginate($where, $time, $limit)
	{
		return $this->scopeQuery(function ($query) use ($where, $time) {
			if (is_array($where) AND count($where) > 0) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->whereHas('agent', function ($query) use ($operate, $va) {
							$query->where('name', $operate, $va)
								->orWhere('mobile', $operate, $va);
						});
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			/*申请时间*/
			if (is_array($time) AND isset($time['created_at'])) {
				$query = $query->whereBetween('created_at', $time['created_at']);
			}

			return $query->orderBy('created_at', 'desc');
		})->paginate($limit);
	}

	public function formatToExcelData($cash)
	{
		$data = [];
		if ($cash AND count($cash) > 0) {
			$i = 0;
			foreach ($cash as $item) {
				$data[$i][] = $item->agent->name;
				$data[$i][] = $item->agent->mobile;
				$data[$i][] = $item->created_at;
				$data[$i][] = $item->cash_no;
				$data[$i][] = $item->amount;
				$data[$i][] = $item->bank_name;
				$data[$i][] = $item->bank_number;
				$data[$i][] = $item->owner_name;
				$data[$i][] = $item->StatusText;
				$i++;
			}
		}

		return $data;
	}

}