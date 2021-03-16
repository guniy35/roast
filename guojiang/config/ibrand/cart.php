<?php

/*
 * This file is part of ibrand/laravel-shopping-cart.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'storage' => \iBrand\Shoppingcart\Storage\DatabaseStorage::class,

    /** @lang guards alias name. */
    'aliases' => [
        'web' => 'default',
        'api' => 'default'
    ]
];
