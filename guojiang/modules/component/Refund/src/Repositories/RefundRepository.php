<?php

namespace GuoJiangClub\Component\Refund\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface RefundRepository extends RepositoryInterface
{
    public function getRefundsByCriteria($andConditions, $orConditions, $limit = 15);
}
