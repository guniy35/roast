<?php

/*
 * This file is part of ibrand/point.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Point\Repository;

interface PointRepository
{
	public function getSumPoint($id, $type = null);

	/**
	 * get valid point total.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getSumPointValid($id);

	public function getSumPointFrozen($id, $type = null);

	public function getSumPointOverValid($id, $type = null);

	/**
	 * 获取积分列表.
	 *
	 * @param     $id
	 * @param int $valid
	 *
	 * @return mixed
	 */
	public function getListPoint($id, $valid = 0);

	/**
	 * @param $itemType
	 * @param $itemId
	 *
	 * @return mixed
	 */
	public function getPointByItem($itemType, $itemId);

	public function getPointsByConditions($where, $limit = 20);

	public function distributePercentage($order);

	public function getDailySumByAction($userId, $action, $day = 0);
}
