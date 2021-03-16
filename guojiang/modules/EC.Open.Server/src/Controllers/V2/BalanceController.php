<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V2;

use GuoJiangClub\Component\Balance\Balance;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\EC\Open\Server\Transformers\BalanceTransformer;

class BalanceController extends Controller
{
    protected $balanceRepository;
    protected $rechargeRuleRepository;
    protected $pay;

    public function __construct(BalanceRepository $balanceRepository)
    {
        $this->balanceRepository = $balanceRepository;
    }

    public function index()
    {
        $type = request('type');
        $limit = request('limit') ? request('limit') : 15;
        $balance = $this->balanceRepository->fluctuation(request()->user()->id, $type)->paginate($limit);

        return $this->response()->paginator($balance, new BalanceTransformer());
    }

    public function sum()
    {
        $user = request()->user();
        $sum = Balance::sumByUser($user->id);
        if (!is_numeric($sum)) {
            $sum = 0;
        } else {
            $sum = (int) $sum;
        }

        return $this->success(compact('sum'));
    }
}
