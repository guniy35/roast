<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$router->post('oauth/sms', 'AuthController@smsLogin')->name('api.oauth.sms');

$router->post('oauth/MiniProgramLogin', 'MiniProgramLoginController@login')->name('api.oauth.miniprogram.login');
$router->post('oauth/MiniProgramMobileLogin', 'MiniProgramLoginController@mobileLogin')->name('api.oauth.miniprogram.mobile.login');
//小程序获取openId
$router->post('oauth/miniprogram/openid', 'MiniProgramLoginController@getOpenIdByCode');

$router->get('store/list', 'GoodsController@index')->name('api.goods.list');
$router->get('store/detail/{id}', 'GoodsController@show')->name('api.goods.detail');
$router->get('store/detail/{id}/stock', 'GoodsController@getStock')->name('api.goods.detail.stock');
$router->get('store/detail/{id}/share/img', 'GoodsController@shareImg')->name('api.goods.detail.share.img');

$router->get('discount/list', 'DiscountController@getDiscountList')->name('api.discount.list');
$router->get('discount/{id}/detail', 'DiscountController@getDiscountDetailByID')->name('api.discount.detail');

$router->get('home', 'CategoryController@index')->name('api.home.index');
$router->get('category', 'CategoryController@category')->name('api.home.category');
$router->get('micro/page/{code}', 'MicroPageController@index')->name('api.micro.page.index');

$router->get('system/settings', 'SystemSettingController@index')->name('api.system.settings');
$router->get('system/init', 'SystemSettingController@init')->name('api.system.init');

$router->post('wechat/notify', 'WechatPayNotifyController@notify');

$router->post('shoppingCart/discount', 'DiscountController@shoppingCartDiscount')->name('api.shopping.cart.discount');

$router->group(['middleware' => ['auth:api']], function ($router) {
    /************************* 购物车 **********************/
    $router->post('shopping/cart', 'ShoppingCartController@store')->name('api.shopping.cart.store');
    $router->get('shopping/cart', 'ShoppingCartController@index')->name('api.shopping.cart');
    $router->put('shopping/cart/{id}', 'ShoppingCartController@update')->name('api.shopping.cart.put');
    $router->delete('shopping/cart/{id}', 'ShoppingCartController@delete')->name('api.shopping.cart.delete');
    $router->post('shopping/cart/clear', 'ShoppingCartController@clear')->name('api.shopping.cart.clear');
    $router->get('shopping/cart/count', 'ShoppingCartController@count')->name('api.shopping.cart.count');

    /************************* 购物流程 **********************/
    $router->post('shopping/order/checkout', 'ShoppingController@checkout')->name('api.shopping.order.checkout');
    $router->post('shopping/order/confirm', 'ShoppingController@confirm')->name('api.shopping.order.confirm');
    $router->post('shopping/order/charge', 'WechatPayController@createCharge')->name('api.shopping.order.charge');
    $router->post('shopping/order/paid', 'PaymentController@paidSuccess')->name('api.shopping.order.paid');
    $router->post('shopping/order/cancel', 'ShoppingController@cancel')->name('api.shopping.order.cancel');
    $router->post('shopping/order/received', 'ShoppingController@received')->name('api.shopping.order.received');
    $router->post('shopping/order/delete', 'ShoppingController@delete')->name('api.shopping.order.delete');
    $router->post('shopping/order/review', 'ShoppingController@review')->name('api.shopping.order.review');
    $router->post('shopping/order/delivery', 'ShoppingController@delivery')->name('api.order.delivery');

    /************************* 收货地址 **********************/
    $router->get('address', 'AddressController@index')->name('api.address.list');
    $router->post('address', 'AddressController@store')->name('api.address.store');
    $router->put('address/{id}', 'AddressController@update')->name('api.address.update');
    $router->get('address/{id}', 'AddressController@show')->where('id', '[0-9]+')->name('api.address.show');
    $router->delete('address/{id}', 'AddressController@delete')->name('api.address.delete');
    $router->get('address/default', 'AddressController@default')->name('api.address.default');

    /*************************** 我的收藏 ********************/
    $router->get('favorite', 'FavoriteController@index')->name('api.favorite');
    $router->post('favorite', 'FavoriteController@store')->name('api.favorite.store');
    $router->delete('favorite', 'FavoriteController@delete')->name('api.favorite.delete');
    $router->post('favorite/delFavs', 'FavoriteController@delFavs')->name('api.favorite.delFavs');
    $router->get('favorite/isfav', 'FavoriteController@getIsFav')->name('api.favorite.isFav');

    /************************* 用户 **********************/
    $router->get('me', 'UserController@me')->name('api.me');
    $router->get('users/ucenter', 'UserController@ucenter')->name('api.user.ucenter');
    $router->post('users/update/info', 'UserController@updateInfo')->name('api.user.update.info');
    $router->post('users/update/mobile', 'UserController@updateMobile')->name('api.user.update.mobile');
    $router->post('users/upload/avatar', 'UserController@uploadAvatar')->name('api.user.upload.avatar');
    $router->get('users/point/list', 'UserController@pointList')->name('api.user.pointList');


    $router->get('order/list', 'OrderController@getOrders')->name('api.order.list');
    $router->get('order/{order_no}', 'OrderController@getOrderDetails')->name('api.order');
    $router->get('order/refund/list', 'OrderController@getRefundOrders')->name('api.order.refund');

    $router->get('coupon', 'CouponController@index')->name('api.coupon.list');
    $router->get('coupon/{id}', 'CouponController@show')->name('api.coupon.show');

    $router->get('users/balance/sum', 'BalanceController@sum')->name('api.user.balance.sum');
    $router->get('users/balance/list', 'BalanceController@index')->name('api.user.balance.list');

    $router->get('users/point', 'WalletController@myPoint')->name('api.user.point');
    /************************* 促销活动和优惠券 **********************/
    $router->post('discount', 'DiscountController@create')->name('api.discount.create');
    $router->post('coupon', 'CouponController@create')->name('api.coupon.create');
    $router->post('coupon/take', 'CouponController@take')->name('api.coupon.take');

    /************************* 售后 **********************/
    $router->get('refund/base_info', 'RefundController@baseInfo')->name('api.refund.baseInfo');
    $router->post('refund/apply', 'RefundController@apply')->name('api.refund.apply');
    $router->get('refund/all', 'RefundController@index')->name('api.refund.all');
    $router->get('refund/show/{refund_no}', 'RefundController@show')->name('api.refund.show');
    $router->get('refund/list', 'RefundController@all')->name('api.refund.list');
    $router->post('refund/user/return', 'RefundController@returnRefund')->name('api.user.return');
    $router->post('refund/user/close', 'RefundController@close')->name('api.user.close');

    /******************评论****************************/
    $router->get('comment/list', 'CommentsController@index')->name('api.comment.list');
});

$router->get('express/query', 'ExpressController@query');

/************************** 小拼团 ******************************/
$router->get('multiGroupon/getGrouponUserList', 'MultGrouponController@getGrouponUserList')->name('api.multiGroupon.getGrouponUserList');
$router->get('multiGroupon/getGrouponItems', 'MultGrouponController@getGrouponItems')->name('api.multiGroupon.getGrouponItems');
$router->get('multiGroupon/showItem', 'MultGrouponController@showItem')->name('api.multiGroupon.showItem');
$router->get('multiGroupon/list', 'MultGrouponController@grouponList')->name('api.multiGroupon.grouponList');
$router->group(['middleware' => ['auth:api']], function ($router) {
    $router->get('multiGroupon/createShareImage', 'MultGrouponController@createShareImage')->name('api.multiGroupon.createShareImage');
    $router->get('multiGroupon/apply', 'MultGrouponController@apply')->name('api.multiGroupon.apply');
});
$router->get('multiGroupon/template', 'MultGrouponController@template')->name('api.multiGroupon.template');

/************************** 砍价 ******************************/
$router->get('reduce/list', 'ReduceController@reduceList')->name('api.reduce.reduceList');
$router->get('reduce/help/text', 'ReduceController@getReduceHelpText')->name('api.reduce.help.text');
$router->get('reduce/showItem', 'ReduceController@showItem')->name('api.reduce.showItem');
$router->group(['middleware' => ['auth:api']], function ($router) {
    $router->get('reduce/list/me', 'ReduceController@me')->name('api.reduce.me');
    $router->post('reduce', 'ReduceController@createReduceItem')->name('api.reduce.createReduceItem');
    $router->post('reduce/help', 'ReduceController@goReduceByUserID')->name('api.reduce.help');
    $router->get('reduce/share', 'ReduceController@createShareImage')->name('api.reduce.createShareImage');
});
$router->get('reduce/template', 'ReduceController@template')->name('api.reduce.template');
