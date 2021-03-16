<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\Component\User\Repository\UserRepository;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private $user;
    private $point;

    public function __construct(
        UserRepository $userRepository,
        PointRepository $pointRepository)
    {
        $this->user = $userRepository;
        $this->point = $pointRepository;
    }

    public function myPoint()
    {
        $id = request()->user()->id;
        $type = request('type');
        $point = $this->point->getSumPoint($id, $type);
        $pointValid = $this->point->getSumPointValid($id);
        $pointFrozen = $this->point->getSumPointFrozen($id, $type);
        $pointOverValid = $this->point->getSumPointOverValid($id, $type);

        $data = [
            'point' => $point,
            'pointValid' => $pointValid,
            'pointFrozen' => $pointFrozen,
            'pointOverValid' => $pointOverValid,
        ];

        return $this->success($data);
    }
}
