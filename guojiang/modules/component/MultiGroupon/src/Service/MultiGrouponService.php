<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon\Service;

use GuoJiangClub\Component\MultiGroupon\Models\MultiGroupon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponItems;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\MultiGroupon\Repositories\MultiGrouponItemRepository;
use GuoJiangClub\Component\MultiGroupon\Repositories\MultiGrouponRepository;
use GuoJiangClub\Component\MultiGroupon\Repositories\MultiGrouponUserRepository;
use iBrand\Common\Wechat\Factory;
use iBrand\Shoppingcart\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;

class MultiGrouponService
{
    protected $multiGrouponRepository;
    protected $multiGrouponUserRepository;
    protected $multiGrouponItemRepository;

    public function __construct(MultiGrouponRepository $multiGrouponRepository, MultiGrouponUserRepository $multiGrouponUserRepository, MultiGrouponItemRepository $multiGrouponItemRepository)
    {
        $this->multiGrouponRepository = $multiGrouponRepository;
        $this->multiGrouponUserRepository = $multiGrouponUserRepository;
        $this->multiGrouponItemRepository = $multiGrouponItemRepository;
    }

    /**
     * 用户下单检测是否已经加入团购.
     *
     * @param $user_id
     * @param $multi_groupon_id
     * @param $multi_groupon_item_id
     *
     * @throws \Exception
     */
    public function checkGrouponStatusByUser($user_id, $multi_groupon_id, $multi_groupon_item_id)
    {
        if ($multi_groupon_item_id) {
            if (MultiGrouponUsers::where(['user_id' => $user_id, 'multi_groupon_id' => $multi_groupon_id, 'multi_groupon_items_id' => $multi_groupon_item_id])->first()) {
                throw new \Exception('已经参团');
            }
        } else {
            if ($this->multiGrouponUserRepository->getGrouponUser($user_id, $multi_groupon_id, 0) or
                $this->multiGrouponUserRepository->getGrouponUserByGrouponID($user_id, $multi_groupon_id)
            ) {
                throw new \Exception('已经参团');
            }
        }
    }

    /**
     * 拼团下单构建下单数据.
     *
     * @param $buys
     * @param $multiGrouponID
     *
     * @return Collection
     */
    public function makeCartItems($buys, $multiGrouponID)
    {
        $cartItems = new Collection();
        $buys_new[] = $buys;
        $MultiGroupon = MultiGroupon::find($multiGrouponID);
        foreach ($buys_new as $k => $item) {
            $__raw_id = md5(time().$k);

            $input = ['__raw_id' => $__raw_id,
                      'id' => $item['id'],    //如果是SKU，表示SKU id，否则是SPU ID
                      'name' => isset($item['name']) ? $item['name'] : '',
                      'img' => isset($item['attributes']['img']) ? $item['attributes']['img'] : '',
                      'qty' => 1, //团购商品数据恒为1
                      'price' => $MultiGroupon->price,
                      'total' => isset($item['total']) ? $item['total'] : '',
            ];

            if (isset($item['attributes']['sku'])) {
                $input['color'] = isset($item['attributes']['color']) ? $item['attributes']['color'] : [];
                $input['size'] = isset($item['attributes']['size']) ? $item['attributes']['size'] : [];
                $input['com_id'] = isset($item['attributes']['com_id']) ? $item['attributes']['com_id'] : [];
                $input['type'] = 'sku';
                $input['__model'] = 'GuoJiangClub\Component\Product\Models\Product';
            } else {
                $input['size'] = isset($item['size']) ? $item['size'] : '';
                $input['color'] = isset($item['color']) ? $item['color'] : '';
                $input['type'] = 'spu';
                $input['__model'] = 'GuoJiangClub\Component\Product\Models\Goods';
                $input['com_id'] = $item['id'];
            }

            $data = new Item(array_merge($input), $item);

            $cartItems->put(md5(time().$k), $data);

            return $cartItems;
        }
    }

    /**
     * 商品详情页获取拼团用户数据.
     *
     * @param $user
     * @param $goods_id
     * @param $multiGrouponItemID
     *
     * @return array
     */
    public function getGrouponUserList($user, $goods_id, $multiGrouponItemID)
    {
        if ($groupon = $this->multiGrouponRepository->getValidGroupByGoodsId($goods_id) or
            ($multiGrouponItemID and $groupon = $this->getGrouponByItemID($multiGrouponItemID))
        ) {
            if ($user and !$multiGrouponItemID) {
                /*如果用户直接进入拼团页面，根据当前用户，查询是否已经参团，主要为了获取itemID，*/
                $where = [
                    'multi_groupon_id' => $groupon->id,
                    'user_id' => $user->id,
                    'status' => 1,
                    'is_leader' => 1,
                ];
                $grouponUser = $this->multiGrouponUserRepository->getGrouponUserByCondition($where);
                if ($grouponUser) {
                    $multiGrouponItemID = $grouponUser->multi_groupon_items_id;
                } else {
                    unset($where['is_leader']);
                    $grouponUser = $this->multiGrouponUserRepository->getGrouponUserByCondition($where);
                    if ($grouponUser) {
                        $multiGrouponItemID = $grouponUser->multi_groupon_items_id;
                    }
                }
            }

            $grouponItem = MultiGrouponItems::find($multiGrouponItemID);

            /*如果是从分享进入页面*/
            $userList = MultiGrouponUsers::where('multi_groupon_items_id', $multiGrouponItemID)
                ->where('status', 1)
                ->orderBy('is_leader', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();

            if (count($userList) > 0) {
                if (0 == $grouponItem->status) { //如果未成团,插入一条空白用户
                    $bankUser = [
                        'user_id' => 0,
                        'multi_groupon_id' => $groupon->id,
                        'multi_groupon_items_id' => $multiGrouponItemID,
                        'meta' => ['avatar' => '', 'nick_name' => ''],
                        'is_leader' => 0,
                        'status' => 1,
                    ];
                    array_splice($userList, 1, 0, [$bankUser]);
                }

                return ['userList' => $userList, 'groupon' => $groupon, 'gap_number' => $grouponItem->getGapNumber(), 'multi_groupon_item_id' => $multiGrouponItemID];
            }
        }

        return ['userList' => [], 'groupon' => [], 'gap_number' => 0, 'multi_groupon_item_id' => $multiGrouponItemID];
    }

    /**
     * 用户进入详情页判断是否已经参团/满团.
     *
     * @param $user_id
     * @param $multiGroupon_id
     *
     * @return array
     */
    public function getJoinStatusByUser($user, $multiGroupon_id, $multiItems_id)
    {
        $joinStatus = 0;  //参团状态
        $completeStatus = 0;  //子团完成状态
        $starts_at = null; //子团开始时间
        $ends_at = null; //子团结束时间
        $orderNo = '';

        $multiGroupon = $this->multiGrouponRepository->find($multiGroupon_id);
        $initStatus = $multiGroupon->init_status;

        if ($multiItems_id) {
            $multiGrouponItem = MultiGrouponItems::find($multiItems_id);
            $initStatus = 1;
            if ($multiGrouponItem->ends_at < Carbon::now() or 2 == $multiGrouponItem->status) {
                /*throw new \Exception('该子团已失效');*/
                $initStatus = 0;
            }

            $completeStatus = $multiGrouponItem->status;
            $starts_at = $multiGrouponItem->starts_at;
            $ends_at = $multiGrouponItem->ends_at;

            //判断用户是否参加了该子团
            if ($user and
                $multiUser = $this->multiGrouponUserRepository->getGrouponUser($user->id, $multiGroupon_id, $multiItems_id)
            ) {
                $joinStatus = 1;
                $orderNo = 1 == $multiUser->order->pay_status ? '' : $multiUser->order->order_no;
            }
        } else {
            if ($user) {
                //如果用户已开团，但是未付款
                if ($multiUser = $this->multiGrouponUserRepository->getGrouponUser($user->id, $multiGroupon_id, 0)
                ) {
                    $joinStatus = 1;
                    $orderNo = 1 == $multiUser->order->pay_status ? '' : $multiUser->order->order_no;
                } elseif ($multiUser = $this->multiGrouponUserRepository->getGrouponUserByGrouponID($user->id, $multiGroupon_id)) {   //如果用户已开团，已付款
                    $joinStatus = 1;
                    $starts_at = $multiUser->grouponItem->starts_at;
                    $ends_at = $multiUser->grouponItem->ends_at;
                    $completeStatus = $multiUser->grouponItem->status;
                    $initStatus = $multiUser->grouponItem->ends_at > Carbon::now() ? 1 : 0;
                }
            }
        }

        return [$joinStatus, $completeStatus, $orderNo, $starts_at, $ends_at, $initStatus];
    }

    /**
     * 生成分享小程序码
     *
     * @param $page
     * @param $width
     * @param $scene
     * @param $freeID
     * @param $user
     *
     * @return bool
     */
    public function createMiniQrcode($page, $width, $scene, $grouponID, $user)
    {
        $option = [
            'page' => $page,
            'width' => $width,
            'scene' => $scene,
        ];

        $app = Factory::miniProgram(config('ibrand.wechat.mini_program.default'));

        $body = $app->app_code->getUnlimit($scene, $option);

        if (str_contains($body, 'errcode')) {
            return false;
        }

        $img_name = $user->id.'_'.'mini_qrcode.jpg';
        $savePath = 'public/multi-groupon/'.$grouponID.'/'.$img_name;
        $result = \Storage::put($savePath, $body);
        if ($result) {
            return $savePath;
        }

        return false;
    }

    public function insertText($qrCodeUrl, $headImg, $saveName, $multiGrouponID, $user)
    {
        $imgPath = storage_path('app/public/multi-groupon/'.$multiGrouponID.'/'.'t_t_.png');

        $img = Image::make($imgPath);

        $img->insert($headImg, 'top-left', 80, 32); //添加头像
        $img->text($user->nick_name, 180, 85, function ($font) {
            $font->file(public_path('assets/backend/distribution/msyh.ttf'));
            $font->size(34);
            $font->color('#ffffff');
        });

        $img->insert($qrCodeUrl, 'bottom-left', 550, 220); //添加二维码
        $img->text('扫描或长按识别小程序码', 600, 1700, function ($font) {
            $font->file(public_path('assets/backend/distribution/msyh.ttf'));
            $font->size(28);
            $font->color('#2E2D2D');
        });

        $img->save($saveName);

        return $saveName;
    }

    /**
     * 根据子团ID获取团信息.
     *
     * @param $multiGrouponItemID
     *
     * @return null
     */
    public function getGrouponByItemID($multiGrouponItemID)
    {
        if ($item = $this->multiGrouponItemRepository->find($multiGrouponItemID)) {
            return $item->groupon;
        }

        return null;
    }
}
