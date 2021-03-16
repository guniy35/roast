<?php

namespace GuoJiangClub\EC\Custom\Demo\Repository;

use GuoJiangClub\Component\Product\Repositories\Eloquent\GoodsRepositoryEloquent as BaseGoodsRepositoryEloquent;

class GoodsRepositoryEloquent extends BaseGoodsRepositoryEloquent
{
    public function testRepository()
    {
        return 'test repository';
    }
}