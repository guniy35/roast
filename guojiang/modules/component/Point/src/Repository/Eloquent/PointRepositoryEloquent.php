<?php

/*
 * This file is part of ibrand/point.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Point\Repository\Eloquent;

use GuoJiangClub\Component\Point\Models\Point;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class PointRepositoryEloquent extends BaseRepository implements PointRepository
{
	/**
	 * Specify Model class name.
	 *
	 * @return string
	 */
	public function model()
	{
		return Point::class;
	}

	public function getSumPoint($id, $type = null)
	{
		$sum = $this->model->where('user_id', $id)->sumPoint();

		return $this->getSumNumeric($sum);
	}

	public function getSumPointFrozen($id, $type = null)
	{
		/*if ($type !== null) {
			$sum = $this->model->where([
				'user_id' => $id,
				'type' => $type,
				'status' => 0
			])->withinTime()->sumPoint();
		} else {
			$sum = $this->model->where('user_id', $id)->where('status', 0)->withinTime()->sumPoint();
		}*/
		$sum = $this->model->where('user_id', $id)->where('status', 0)->withinTime()->sumPoint();

		return $this->getSumNumeric($sum);
	}

	public function getSumPointOverValid($id, $type = null)
	{
		$sum = $this->model->where('user_id', $id)->overValid()->sumPoint();

		return $this->getSumNumeric($sum);
	}

	private function getSumNumeric($sum)
	{
		if (!is_numeric($sum)) {
			return 0;
		}

		return $sum;
	}

	public function getSumPointValid($user_id)
	{
		return $this->model->where('user_id', $user_id)->valid()->sum('value');
	}

	public function getListPoint($id, $valid = 0)
	{
		$query = $this->model->where('user_id', $id);
		if (0 == $valid) {
			$query = $query->get();
		} elseif (1 == $valid) {
			$query = $query->valid()->get();
		} else {
			$query = $query->overValid()->get();
		}

		return $query;
	}

	/**
	 * get point by item's type and item's id;.
	 *
	 * @param $itemType
	 * @param $itemId
	 *
	 * @return mixed
	 */
	public function getPointByItem($itemType, $itemId)
	{
		return $this->findWhere(['item_type' => $itemType, 'item_id' => $itemId])->first();
	}

	public function getPointsByConditions($where, $limit = 20)
	{
		$this->applyConditions($where);

		return $this->orderBy('created_at', 'desc')->paginate($limit);
	}

	public function distributePercentage($order)
	{
		if (!$adjustment = $order->getAdjustments() or !$adjustment = $adjustment->where('origin_type', 'point')->first()) {
			return false;
		}
		$amount              = (-1) * $adjustment->amount;
		$splitDiscountAmount = [];
		$numberOfTargets     = $order->countItems();
		$percentageTotal     = 100;
		$i                   = 1;
		$items               = $order->getItems();
		foreach ($items as $item) {
			if ($i > $numberOfTargets) {
				break;
			}
			if ($i == $numberOfTargets) {
				$percentageItem = $percentageTotal;
			} else {
				//因为Backend下的Order模型定义了items_total获取时自动 / 100，percentageItem计算时原本应该 * 100，这里没有处理
				//所以此方法暂时只适用于Backend下导出订单
				$percentageItem  = (int) ($item->units_total / $order->items_total);
				$percentageTotal -= $percentageItem;
			}
			$splitDiscountAmount[] = [
				'item_id' => $item->id,
				'value'   => (int) ($amount * $percentageItem / 100),
			];
			++$i;
		}

		return $splitDiscountAmount;
	}

	public function getDailySumByAction($userId, $action, $day = 0)
	{
		$query = $this->model->where('action', $action)->where('user_id', $userId);
		if ($day == 0) {
			$query = $query->whereRaw("DATE_FORMAT(el_point.created_at, '%Y%m%d') = DATE_FORMAT(CURDATE() , '%Y%m%d')");
		} else {
			//暂未实现
		}

		return $query->sum('value');
	}
}
