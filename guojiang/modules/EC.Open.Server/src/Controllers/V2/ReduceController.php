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

use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use GuoJiangClub\Component\Reduce\Repositories\ReduceItemsRepository;
use GuoJiangClub\Component\Reduce\Repositories\ReduceRepository;
use GuoJiangClub\Component\Reduce\Service\ReduceService;
use GuoJiangClub\EC\Open\Server\Transformers\ReduceItemsTransformer;
use GuoJiangClub\EC\Open\Server\Transformers\ReduceTransformer;
use iBrand\Miniprogram\Poster\MiniProgramShareImg;

class ReduceController extends Controller
{
    protected $reduceRepository;

    protected $reduceService;

    protected $reduceItemsRepository;

    public function __construct(ReduceRepository $reduceRepository, ReduceService $reduceService, ReduceItemsRepository $reduceItemRepository
    ) {
        $this->reduceRepository = $reduceRepository;

        $this->reduceService = $reduceService;

        $this->reduceItemsRepository = $reduceItemRepository;
    }

    /**
     * 砍价列表.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function reduceList()
    {
        $limit = request('limit') ? request('limit') : 10;

        $list = $this->reduceRepository->getReduceList($limit);

        return $this->response()->paginator($list, new ReduceTransformer());
    }

    /**
     * 发起砍价.
     *
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function createReduceItem()
    {
        $user = auth('api')->user();

        $reduce_id = request('reduce_id');

        $restart = request('restart');

        $res = $this->reduceService->createReduceItem($user->id, $reduce_id, $restart);

        if (isset($res['errormsg'])) {
            return $this->failed($res['errormsg']);
        }

        return $this->success($res);
    }

    /**
     * 砍价详情.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function showItem()
    {
        $reduce_items_id = request('reduce_items_id');

        $res = $this->reduceItemsRepository->getReduceItemByID($reduce_items_id);

        return $this->success($res);
    }

    /**
     * 我的砍价列表.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function me()
    {
        $user = auth('api')->user();

        $limit = request('limit') ? request('limit') : 10;

        $list = $this->reduceItemsRepository->getReduceItemByUserID($user->id, $limit);

        return $this->response()->paginator($list, new ReduceItemsTransformer());
    }

    /**
     * 去砍价.
     *
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function goReduceByUserID()
    {
        $user = auth('api')->user();

        $reduce_items_id = request('reduce_items_id');

        $res = $this->reduceService->goReduceByUserID($user->id, $reduce_items_id);

        if (isset($res['errormsg'])) {
            return $this->failed($res['errormsg']);
        }

        return $this->success($res);
    }

    /**
     * 砍价活动规则.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function getReduceHelpText()
    {
        $data['reduce_help_text'] = settings('reduce_help_text');

        return $this->success($data);
    }

    public function template()
    {
        $reduce_items_id = request('reduce_items_id');

        $reduceItems = $this->reduceItemsRepository->getReduceItemByID($reduce_items_id);

        if (!$reduceItems) {
            return $this->failed('活动不存在');
        }

        $page = 'pages/bargain/details/details';

        $mini_qrcode = $this->reduceService->createMiniQrcode($page, 420, $reduce_items_id, $reduce_items_id);

        if (!$mini_qrcode) {
            return $this->failed('生成二维码失败，请重试', 400, false);
        }

        $mini_qrcode = env('APP_URL').'/storage/reduce/'.$reduce_items_id.'/'.'mini_qrcode.jpg';

        return view('server::share.reduce', compact('reduceItems', 'mini_qrcode'));
    }

    public function createShareImage()
    {
        $reduce_items_id = request('reduce_items_id');

        $user = auth('api')->user();

        $reduceItems = ReduceItems::find($reduce_items_id);

        if (!$reduceItems) {
            return $this->failed('活动不存在');
        }

        if ($reduceItems->share_img) {
            return $this->success(['image' => $reduceItems->share_img]);
        }

        $route = url('api/reduce/template?reduce_items_id='.$reduce_items_id);

        $data = MiniProgramShareImg::run($reduceItems, $route);
        if ($data) {
            $reduceItems->share_img = $data['url'];
            $reduceItems->save();

            return $this->success(['image' => $data['url']]);
        }

        return $this->failed('生成失败', 400, false);
    }
}
