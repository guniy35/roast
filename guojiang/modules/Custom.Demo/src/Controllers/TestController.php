<?php

namespace GuoJiangClub\EC\Custom\Demo\Controllers;

use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use iBrand\Common\Controllers\Controller;

class TestController extends Controller
{
    protected $goodsRepository;

    public function __construct(GoodsRepository $goodsRepository)
    {
        $this->goodsRepository = $goodsRepository;
    }

    public function testRepository()
    {
        //测试发现，引用的 GoodsRepository 接口，但是初始化的对象是 Demo 中的 GoodsRepositoryEloquent
        return $this->goodsRepository->testRepository();
    }

    public function testModel()
    {
        //测试发现，实例化的对象是： GuoJiangClub\EC\Custom\Demo\Models\Goods ，也是 DEMO 中重新绑定的接口实现。
        dd(get_class($this->goodsRepository->first()));
    }

    public function goodsList()
    {
        return '覆盖原有的路由';
    }
}