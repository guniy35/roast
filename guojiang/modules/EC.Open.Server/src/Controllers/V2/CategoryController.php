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

use DB;
use GuoJiangClub\Component\Advert\Models\MicroPage;
use GuoJiangClub\Component\Advert\Models\MicroPageAdvert;
use GuoJiangClub\Component\Advert\Repositories\AdvertItemRepository;

class CategoryController extends Controller
{
    private $advertItem;
    protected $microPage;
    protected $microPageAdvert;

    public function __construct(AdvertItemRepository $advertItemRepository, MicroPage $microPage, microPageAdvert $microPageAdvert)
    {
        $this->advertItem = $advertItemRepository;
        $this->microPage = $microPage;
        $this->microPageAdvert = $microPageAdvert;
    }

    public function index()
    {
    }

    public function category()
    {
        $microPage = $this->microPage->where('page_type', MicroPage::PAGE_TYPE_Category)->first();

        if (!$microPage) {
            return $this->success();
        }

        $microPageAdverts = $this->microPageAdvert->where('micro_page_id', $microPage->id)
            ->with(['advert' => function ($query) {
                return $query = $query->where('status', 1);
            }])
            ->orderBy('sort')->get();

        if ($microPageAdverts->count()) {
            $i = 0;

            foreach ($microPageAdverts as $key => $item) {
                if ($item->advert_id > 0) {
                    if ('micro_page_componet_category' == $item->advert->type) {
                        $data['pages'][$i]['name'] = $item->advert->type;

                        $data['pages'][$i]['title'] = $item->advert->title;

                        $data['pages'][$i]['is_show_title'] = $item->advert->is_show_title;

                        $advertItem = $this->getAdvertItem($item->advert->code, []);

                        $data['pages'][$i]['value'] = array_values($advertItem);

                        ++$i;
                    }
                }
            }
        }

        $data['micro_page'] = $microPage;

        return $this->success($data);
    }

    public function getAdvertItem($code, $associate_with)
    {
        $advertItem = $this->advertItem->getItemsByCode($code, $associate_with);

        if ($advertItem->count()) {
            $filtered = $advertItem->filter(function ($item) {
                if (!$item->associate and $item->associate_id) {
                    return [];
                }

                switch ($item->associate_type) {
                    case 'category':

                        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

                        $category_id = $item->associate_id;

                        $categoryGoodsIds = DB::table($prefix.'goods_category')
                            ->where('category_id', $category_id)
                            ->select('goods_id')->distinct()->get()
                            ->pluck('goods_id')->toArray();

                        $goodsList = DB::table($prefix.'goods')
                            ->whereIn('id', $categoryGoodsIds)
                            ->where('is_del', 0)
                            ->limit($item->meta['limit'])->get();

                        $item->goodsList = $goodsList;

                        return $item;

                        break;

                    default:

                        return $item;
                }
            });

            return $filtered->all();
        }

        return $advertItem;
    }
}
