<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Service;

use Carbon\Carbon;
use GuoJiangClub\Component\Reduce\Models\ReduceItems;
use GuoJiangClub\Component\Reduce\Models\ReduceUsers;
use GuoJiangClub\Component\Reduce\Repositories\ReduceItemsRepository;
use GuoJiangClub\Component\Reduce\Repositories\ReduceRepository;
use GuoJiangClub\Component\Reduce\Repositories\ReduceUserRepository;
use GuoJiangClub\Component\User\Models\User;
use iBrand\Common\Wechat\Factory;
use iBrand\Shoppingcart\Item;
use Illuminate\Support\Collection;

class ReduceService
{
    protected $reduceRepository;

    protected $reduceUserRepository;

    protected $reduceItemRepository;

    public function __construct(ReduceRepository $reduceRepository, ReduceUserRepository $reduceUserRepository, ReduceItemsRepository $reduceItemRepository
    ) {
        $this->reduceRepository = $reduceRepository;
        $this->reduceUserRepository = $reduceUserRepository;
        $this->reduceItemRepository = $reduceItemRepository;
    }

    /**
     * 发起砍价.
     *
     * @param $user_id
     * @param $reduce_id
     * @param bool $restart
     *
     * @return array|mixed|null
     */
    public function createReduceItem($user_id, $reduce_id, $restart = false)
    {
        $reduce = $this->reduceRepository->findByField('id', $reduce_id)->first();

        if (!$reduce || $reduce->store_nums <= 0 || '进行中' != $reduce->status_text) {
            $status_text = isset($reduce->status_text) ? $reduce->status_text : '不存在';

            return ['errormsg' => '该砍价活动'.$status_text];
        }

        $reduceUser = $this->reduceUserRepository->getReduceUserByReduceID($user_id, $reduce_id);

        if (!$reduceUser) {
            $reduceUser = $this->createReduceItemAndReduceUser($user_id, $reduce);
        }

        if ($restart) {
            if ($reduceUser and $reduceItems = $reduceUser->reduceItem) {
                $reduceItems->status = ReduceItems::STATUS_END;

                $reduceItems->order_id = null;

                $reduceItems->ends_at = Carbon::now()->toDateTimeString();

                $reduceItems->users->filter(function ($item) {
                    $item->status = ReduceUsers::STATUS_NEW;

                    $item->save();
                });

                $reduceItems->save();
            }

            $reduceUser = $this->createReduceItemAndReduceUser($user_id, $reduce);
        }

        return $reduceUser;
    }

    protected function createReduceItemAndReduceUser($user_id, $reduce)
    {
        $meta = $this->getMeta($user_id);

        $time = Carbon::now();

        $reduceRule = new \GuoJiangClub\Component\Reduce\Rule\ReduceRule();

        $reduce_amount_arr = $reduceRule->getReduce($reduce->reduce_total, $reduce->number)['items'];

        $reduceItemData = ['reduce_id' => $reduce->id, 'user_id' => $user_id,
            'reduce_goods_id' => $reduce->goods_id,
            'starts_at' => $time->toDateTimeString(),
            'ends_at' => $time->addHour($reduce->hour)->toDateTimeString(),
            'reduce_amount_arr' => json_encode($reduce_amount_arr),
        ];

        $reduceItem = $this->reduceItemRepository->create($reduceItemData);

        $reduceUser = $this->reduceUserRepository->create(['user_id' => $user_id,
            'reduce_id' => $reduce->id,
            'reduce_amount' => $reduce_amount_arr[0],
            'is_leader' => 1,
            'meta' => $meta,
            'reduce_items_id' => $reduceItem->id, ]);

        return $reduceUser;
    }

    /**
     * 去砍价.
     *
     * @param $user_id
     * @param $reduce_items_id
     *
     * @return array|mixed
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function goReduceByUserID($user_id, $reduce_items_id)
    {
        $reduceItem = $this->reduceItemRepository->getReduceItemByID($reduce_items_id);

        if (!$reduceItem || !$reduceItem->reduce || $reduceItem->reduce->store_nums <= 0 || '进行中' != $reduceItem->status_text) {
            $status_text = isset($reduceItem->status_text) ? $reduceItem->status_text : '不存在';

            return ['errormsg' => '该砍价活动'.$status_text];
        }

        if ('进行中' != $reduceItem->status_text) {
            return ['errormsg' => '你来晚啦该砍价已结束'];
        }

        if ($reduceItem->user_id == $user_id) {
            return ['errormsg' => '你已经砍过啦，快去分享好友帮忙砍价吧'];
        }

        if (1 == $reduceItem->progress_par) {
            return ['errormsg' => '已经是最低价了不能再砍啦'];
        }

        $reduceUser = $this->reduceUserRepository->findWhere(['user_id' => $user_id, 'is_leader' => 0, 'reduce_id' => $reduceItem->reduce_id, 'status' => ReduceUsers::STATUS_NEW]);

        if (count($reduceUser)) {
            return ['errormsg' => '该砍价活动商品你已经砍过啦'];
        }

        $reduceUserCount = $this->reduceUserRepository->findWhere(['reduce_id' => $reduceItem->reduce_id, 'status' => ReduceUsers::STATUS_NEW, 'reduce_items_id' => $reduce_items_id])->count();

        $reduce_amount = json_decode($reduceItem->reduce_amount_arr)[$reduceUserCount];

        $meta = $this->getMeta($user_id);

        return $this->reduceUserRepository->create(
            ['user_id' => $user_id,
                'reduce_id' => $reduceItem->reduce_id,
                'reduce_amount' => $reduce_amount,
                'meta' => $meta,
                'reduce_items_id' => $reduceItem->id, ]);
    }

    /**
     * 用户下单检测砍价是否有效.
     *
     * @param $user_id
     * @param $reduce_items_id
     *
     * @throws \Exception
     */
    public function checkReduceStatusByUser($user_id, $reduce_items_id)
    {
        if ($reduceItem = ReduceItems::where(['user_id' => $user_id, 'id' => $reduce_items_id])->first()) {
            if (!$reduceItem || !$reduceItem->reduce || $reduceItem->reduce->store_nums <= 0) {
                throw new \Exception('该砍价活动已结束');
            }

            if ($reduceItem->status || $reduceItem->order_id || $reduceItem->complete_time) {
                throw new \Exception('该砍价已结算或已失效');
            }

            if ($reduceItem->ends_at <= Carbon::now() and 1 != $reduceItem->progress_par) {
                throw new \Exception('该砍价已超时过期');
            }
        }
    }

    /**
     * 砍价下单构建下单数据.
     *
     * @param $buys
     * @param $reduce_items_id
     *
     * @return Collection
     */
    public function makeCartItems($buys, $reduce_items_id)
    {
        $cartItems = new Collection();
        $buys_new[] = $buys;
        $reduce_items = ReduceItems::find($reduce_items_id);

        foreach ($buys_new as $k => $item) {
            $__raw_id = md5(time().$k);

            $input = ['__raw_id' => $__raw_id,
                'id' => $item['id'],    //如果是SKU，表示SKU id，否则是SPU ID
                'name' => isset($item['name']) ? $item['name'] : '',
                'img' => isset($item['attributes']['img']) ? $item['attributes']['img'] : '',
                'qty' => 1, //商品数据恒为1
                'price' => $reduce_items->time_price,
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

    protected function getMeta($user_id)
    {
        $user = User::find($user_id);
        $meta['avatar'] = $user->avatar;
        $meta['nick_name'] = $user->nick_name;

        return $meta;
    }

    /**
     * 生成分享小程序码
     *
     * @param $page
     * @param $width
     * @param $scene
     * @param $grouponID
     * @param $user
     *
     * @return bool|string
     */
    public function createMiniQrcode($page, $width, $scene, $reduce_items_id)
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

        $img_name = 'mini_qrcode.jpg';
        $savePath = 'public/reduce/'.$reduce_items_id.'/'.$img_name;
        $result = \Storage::put($savePath, $body);
        if ($result) {
            return $savePath;
        }

        return false;
    }
}
