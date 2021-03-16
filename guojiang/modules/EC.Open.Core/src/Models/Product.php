<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Core\Models;

use GuoJiangClub\EC\Open\Core\Discount\Contracts\DiscountItemContract;

class Product extends \GuoJiangClub\Component\Product\Models\Product implements DiscountItemContract
{
    /**
     * get item categories.
     *
     * @return mixed
     */
    public function getCategories()
    {
        return $this->goods->getCategories();
    }
}
