<?php

$router->group(['prefix' => 'admin/store'], function () use ($router) {
	$router->post('upload/image', 'ImageController@postUpload')->name('upload.image');
	$router->post('upload/excel', 'ImageController@ExcelUpload')->name('upload.excel');
	$router->post('upload/uploadExcelFile', 'ImageController@uploadExcelFile')->name('upload.uploadExcelFile');

	$router->get('dashboard', 'DashboardController@dashboard')->name('admin.store.dashboard.index');
	$router->get('getMonthData', 'DashboardController@getMonthData')->name('admin.store.dashboard.getMonthData');
	
	$router->group(['prefix' => 'setting'], function () use ($router) {
		$router->get('shopSetting', 'SystemSettingController@shopSetting')->name('admin.setting.shopSetting');
		$router->post('saveShopSetting', 'SystemSettingController@saveSettings')->name('admin.setting.saveShopSetting');
		$router->get('point', 'SystemSettingController@point')->name('admin.setting.point');
		$router->get('refund-reason', 'SystemSettingController@refundReason')->name('admin.setting.refund.reason');
		$router->post('saveRefundSettings', 'SystemSettingController@saveRefundSettings')->name('admin.setting.saveRefundSettings');
		$router->get('onlineService', 'SystemSettingController@onlineService')->name('admin.setting.onlineService');
		$router->post('saveOnlineService', 'SystemSettingController@saveOnlineService')->name('admin.setting.saveOnlineService');

		$router->group(['prefix' => 'micro/page', 'namespace' => 'MicroPage'], function () use ($router) {
			$router->get('/', 'MicroPageController@index')->name('admin.setting.micro.page.index');
			$router->get('/store', 'MicroPageController@store')->name('admin.setting.micro.page.store');
			$router->post('/{id}/delete', 'MicroPageController@delete')->name('admin.setting.micro.page.delete');
			$router->post('/{id}/setIndexPage', 'MicroPageController@setIndexPage')->name('admin.setting.micro.page.setIndexPage');
			$router->post('/{id}/setCategoryPage', 'MicroPageController@setCategoryPage')->name('admin.setting.micro.page.setCategoryPage');

			$router->post('/name/update', 'MicroPageController@update')->name('admin.setting.micro.page.name.update');
			$router->post('/{id}/updateMicroPageAd', 'MicroPageController@updateMicroPageAd')->name('admin.setting.micro.page.updateMicroPageAd');
			$router->get('/{id}/edit', 'MicroPageController@edit')->name('admin.setting.micro.page.name.edit');
			$router->get('/get/advert', 'MicroPageController@getAdvertByType')->name('admin.setting.micro.page.get.advert');
			$router->post('compoent/{id}/delete', 'CompoentController@delete')->name('admin.setting.micro.page.compoent.delete');
			$router->post('/update', 'CompoentController@update')->name('admin.setting.micro.page.compoent.update');
			$router->post('/store', 'CompoentController@store')->name('admin.setting.micro.page.compoent.store');
			$router->get('/model/goods', 'CompoentController@modelGoods')->name('admin.setting.micro.page.compoent.model.goods');
			$router->get('/model/coupons', 'CompoentController@modelCoupons')->name('admin.setting.micro.page.compoent.model.coupons');
			$router->get('/model/categorys', 'CompoentController@modelCategorys')->name('admin.setting.micro.page.compoent.model.categorys');
			$router->get('/model/pages', 'CompoentController@modelPages')->name('admin.setting.micro.page.compoent.model.pages');
			$router->get('/model/seckills', 'CompoentController@modelSeckills')->name('admin.setting.micro.page.compoent.model.seckills');
			$router->get('/model/images', 'CompoentController@modelImages')->name('admin.setting.micro.page.compoent.model.images');
			$router->get('/model/brands', 'CompoentController@modelBrands')->name('admin.setting.micro.page.compoent.model.brands');
			$router->get('/model/groupons', 'CompoentController@modelGroupons')->name('admin.setting.micro.page.compoent.model.groupons');
			$router->get('/model/suits', 'CompoentController@modelSuits')->name('admin.setting.micro.page.compoent.model.suits');
			$router->get('/model/articles', 'CompoentController@modelArticles')->name('admin.setting.micro.page.compoent.model.articles');
			$router->get('compoent/getArticlesData', 'CompoentController@getArticlesData')->name('admin.setting.micro.page.compoent.getArticlesData');
			$router->get('/model/images', 'CompoentController@modelImages')->name('admin.setting.micro.page.compoent.model.images');
			$router->get('/model/groupons', 'CompoentController@modelGroupons')->name('admin.setting.micro.page.compoent.model.groupons');
			$router->get('compoent/getGoodsData', 'CompoentController@getGoodsData')->name('admin.setting.micro.page.compoent.getGoodsData');
			$router->get('compoent/getPagesData', 'CompoentController@getPagesData')->name('admin.setting.micro.page.compoent.getPagesData');
			$router->get('compoent/getCategorysData', 'CompoentController@getCategorysData')->name('admin.setting.micro.page.compoent.getCategorysData');
			$router->get('compoent/getCouponsData', 'CompoentController@getCouponsData')->name('admin.setting.micro.page.compoent.getCouponsData');
			$router->get('compoent/getGrouponsData', 'CompoentController@getGrouponsData')->name('admin.setting.micro.page.compoent.getGrouponsData');
			$router->get('compoent/getSeckillsData', 'CompoentController@getSeckillsData')->name('admin.setting.micro.page.compoent.getSeckillsData');
			$router->get('compoent/getSuitsData', 'CompoentController@getSuitsData')->name('admin.setting.micro.page.compoent.getSuitsData');

			$router->group(['prefix' => 'compoent/'], function () use ($router) {
				$router->get('/', 'CompoentController@index')->name('admin.setting.micro.page.compoent.index');
				$router->get('{type}/', 'CompoentController@index')->name('admin.setting.micro.page.compoent.index');
				$router->get('{type}/create', 'CompoentController@create')->name('admin.setting.micro.page.compoent.create');
				$router->get('{type}/{code}/edit', 'CompoentController@edit')->name('admin.setting.micro.page.compoent.edit');
			});
		});
	});

	//新的规格管理
	$router->group(['prefix' => 'specs'], function () use ($router) {

		$router->get('/', 'GoodsSpecController@index')->name('admin.goods.spec.index');
		$router->get('create', 'GoodsSpecController@create')->name('admin.goods.spec.create');
		$router->post('store', 'GoodsSpecController@store')->name('admin.goods.spec.store');
		$router->get('edit/{id}', 'GoodsSpecController@edit')->name('admin.goods.spec.edit');

		$router->get('specValue/{id}', 'GoodsSpecController@specValue')->name('admin.goods.spec.value.index');
		$router->post('getSpeValueData', 'GoodsSpecController@getSpeValueData')->name('admin.goods.spec.getSpeValueData');
		$router->post('specValue/store', 'GoodsSpecController@specValueStore')->name('admin.goods.spec.value.store');

		$router->get('editSpecValue', 'GoodsSpecController@editSpecValue')->name('admin.goods.spec.value.editSpecValue');
		$router->post('storeSpecValue', 'GoodsSpecController@storeSpecValue')->name('admin.goods.spec.value.storeSpecValue');
		$router->get('addSpecValue/{spec_id}', 'GoodsSpecController@addSpecValue')->name('admin.goods.spec.value.addSpecValue');

		$router->post('delSpecValue', 'GoodsSpecController@delSpecValue')->name('admin.goods.spec.value.delete');

		$router->post('delete/{id}', 'GoodsSpecController@destroy')->name('admin.goods.spec.delete');
	});

	//新模型管理
	$router->group(['prefix' => 'models'], function () use ($router) {

		$router->get('/', 'GoodsModelsController@index')->name('admin.goods.model.index');
		$router->get('create', 'GoodsModelsController@create')->name('admin.goods.model.create');
		$router->post('store', 'GoodsModelsController@store')->name('admin.goods.model.store');
		$router->get('edit/{id}', 'GoodsModelsController@edit')->name('admin.goods.model.edit');

		$router->post('delete/{id}', 'GoodsModelsController@delete')->name('admin.goods.model.delete');
		$router->post('deleteAttrValue/{id}', 'GoodsModelsController@deleteAttrValue')->name('admin.goods.model.deleteAttrValue');
		$router->post('deleteAttr/{id}', 'GoodsModelsController@deleteAttr')->name('admin.goods.model.deleteAttr');
		$router->post('checkSpec/{id}/{model_id}', 'GoodsModelsController@checkSpec')->name('admin.goods.model.checkSpec');
	});

	//公用属性管理
	$router->group(['prefix' => 'attribute'], function () use ($router) {

		$router->get('/', 'GoodsAttributeController@index')->name('admin.goods.attribute.index');
		$router->get('create', 'GoodsAttributeController@create')->name('admin.goods.attribute.create');
		$router->post('store', 'GoodsAttributeController@store')->name('admin.goods.attribute.store');
		$router->get('edit/{id}', 'GoodsAttributeController@edit')->name('admin.goods.attribute.edit');

		$router->post('delete/{id}', 'GoodsAttributeController@delete')->name('admin.goods.attribute.delete');
	});

	//新产品
	$router->group(['prefix' => 'goods'], function () use ($router) {
		$router->get('/', 'CommodityController@index')->name('admin.goods.index');
		$router->get('createBefore', 'CommodityController@createBefore')->name('admin.goods.createBefore');
		$router->get('create', 'CommodityController@create')->name('admin.goods.create');
		$router->get('edit/{id}', 'CommodityController@edit')->name('admin.goods.edit');
		$router->get('sort/update', 'CommodityController@updateSort')->name('admin.goods.sort.update');

		$router->get('excel', 'CommodityController@excel')->name('admin.goods.excel');

		$router->post('destroy/{id}', 'CommodityController@destroy')->name('admin.goods.destroy');
		$router->post('delete/{id}', 'CommodityController@delete')->name('admin.goods.delete');
		$router->post('restore/{id}', 'CommodityController@restore')->name('admin.goods.restore');

		$router->get('get_category', 'CommodityController@getCategoryByGroupID')->name('admin.goods.get_category');
		$router->get('uploadStock', 'CommodityController@uploadStock')->name('admin.goods.uplode_inventorys');

		$router->post('doUploadStock', 'CommodityController@doUploadStock')->name('admin.goods.inventorys_insert');

		$router->post('store', 'CommodityController@store')->name('admin.goods.store');
		$router->get('getAttribute', 'CommodityController@getAttribute')->name('admin.goods.getAttribute');
		$router->get('getSpecsData', 'CommodityController@getSpecsData')->name('admin.goods.getSpecsData');

		$router->get('getExportData', 'CommodityController@getExportData')->name('admin.goods.getExportData');

		$router->get('operationTitle', 'CommodityController@operationTitle')->name('admin.goods.operationTitle');
		$router->post('saveTitle', 'CommodityController@saveTitle')->name('admin.goods.saveTitle');

		$router->get('operationTags', 'CommodityController@operationTags')->name('admin.goods.operationTags');
		$router->post('saveTags', 'CommodityController@saveTags')->name('admin.goods.saveTags');

		$router->post('checkPromotionStatus', 'CommodityController@checkPromotionStatus')->name('admin.goods.checkPromotionStatus');
		$router->post('saveIsDel', 'CommodityController@saveIsDel')->name('admin.goods.saveIsDel');

		$router->get('/limit', 'GoodsPurchaseController@index')->name('admin.store.goods.limit');
		$router->get("/limit/sync", "GoodsPurchaseController@syncGoods")->name('admin.store.goods.limit.syncGoods');
		$router->get("/limit/editGoods", "GoodsPurchaseController@editGoods")->name('admin.store.goods.limit.editGoods');
		$router->post("/limit/saveGoods", "GoodsPurchaseController@saveGoods")->name('admin.store.goods.limit.saveGoods');
		$router->post("/limit/postSyncGoods", "GoodsPurchaseController@postSyncGoods")->name('admin.store.goods.limit.postSyncGoods');
		$router->get("/limit/editBatchGoods", "GoodsPurchaseController@editBatchGoods")->name('admin.store.goods.limit.editBatchGoods');
		$router->post("/limit/saveBatchGoods", "GoodsPurchaseController@saveBatchGoods")->name('admin.store.goods.limit.saveBatchGoods');
	});

	//品牌
	$router->resource('brand', 'BrandController');

	//分类
	$router->get('category', 'CategoryController@index')->name('admin.category.index');
	$router->get('category/create', 'CategoryController@create')->name('admin.category.create');
	$router->post('category/store', 'CategoryController@store')->name('admin.category.store');
	$router->get('category/edit/{id}', 'CategoryController@edit')->name('admin.category.edit');
	$router->post('category/update/{id}', 'CategoryController@update')->name('admin.category.update');
	$router->get('category/check', 'CategoryController@check')->name('admin.category.check');
	$router->post('category/delete', 'CategoryController@destroy')->name('admin.category.delete');

	$router->get('category/category_sort', 'CategoryController@category_sort')->name('admin.category.category_sort');
});

//促销
$router->group(['prefix' => 'admin/store/promotion'], function () use ($router) {
	//新促销活动
	$router->group(['prefix' => 'discount', 'namespace' => 'Promotion'], function () use ($router) {
		$router->get('/', 'DiscountController@index')->name('admin.promotion.discount.index');
		$router->get('create', 'DiscountController@create')->name('admin.promotion.discount.create');
		$router->get('edit/{id}', 'DiscountController@edit')->name('admin.promotion.discount.edit');
		$router->post('store', 'DiscountController@store')->name('admin.promotion.discount.store');
		$router->get('useRecord', 'DiscountController@useRecord')->name('admin.promotion.discount.useRecord');

		$router->get('getUsedExportData', 'DiscountController@getUsedExportData')->name('admin.promotion.discount.getUsedExportData');
	});

	//新促销优惠券
	$router->group(['prefix' => 'coupon', 'namespace' => 'Promotion'], function () use ($router) {
		$router->get('/', 'CouponController@index')->name('admin.promotion.coupon.index');
		$router->get('create', 'CouponController@create')->name('admin.promotion.coupon.create');
		$router->get('edit/{id}', 'CouponController@edit')->name('admin.promotion.coupon.edit');
		$router->post('store', 'CouponController@store')->name('admin.promotion.coupon.store');
		$router->get('useRecord', 'CouponController@useRecord')->name('admin.promotion.coupon.useRecord');

		$router->get('show', 'CouponController@showCoupons')->name('admin.promotion.coupon.show');

		$router->get('couponCode', 'CouponController@couponCode')->name('admin.promotion.coupon.couponCode');
		$router->post('createCouponCode', 'CouponController@createCouponCode')->name('admin.promotion.coupon.createCouponCode');
		$router->get('getExportData', 'CouponController@getExportData')->name('admin.promotion.coupon.getExportData');

		$router->get('getUsedExportData', 'CouponController@getUsedExportData')->name('admin.promotion.coupon.getUsedExportData');
		$router->get('getCouponsExportData', 'CouponController@getCouponsExportData')->name('admin.promotion.coupon.getCouponsExportData');
	});

	$router->group(['prefix' => 'pubic', 'namespace' => 'Promotion'], function () use ($router) {
		$router->get('getSpu', 'PublicController@getSpu')->name('admin.promotion.getSpu');
		$router->get('getWechatGroup', 'PublicController@getWechatGroup')->name('admin.promotion.getWechatGroup');
		$router->post('getSpuData', 'PublicController@getSpuData')->name('admin.promotion.getSpuData');
		$router->post('getWechatGroupData', 'PublicController@getWechatGroupData')->name('admin.promotion.getWechatGroupData');
		$router->post('export/excelExport', 'PublicController@excelExport')->name('admin.promotion.excelExport');
		$router->get('export/download/{url}', 'PublicController@download')->name('admin.promotion.download');
	});

	//多人拼团
	$router->group(['prefix' => 'multiGroupon'], function () use ($router) {
		$router->get('/', 'MultiGrouponController@index')->name('admin.promotion.multiGroupon.index');
		$router->get('create', 'MultiGrouponController@create')->name('admin.promotion.multiGroupon.create');
		$router->get('edit/{id}', 'MultiGrouponController@edit')->name('admin.promotion.multiGroupon.edit');
		$router->post('store', 'MultiGrouponController@store')->name('admin.promotion.multiGroupon.store');
		$router->get('getSpuModal', 'MultiGrouponController@getSpuModal')->name('admin.promotion.multiGroupon.getSpuModal');
		$router->post('getSpuData', 'MultiGrouponController@getSpuData')->name('admin.promotion.multiGroupon.getSpuData');
		$router->post('update', 'MultiGrouponController@update')->name('admin.promotion.multiGroupon.update');
		$router->post('delete/{id}', 'MultiGrouponController@delete')->name('admin.promotion.multiGroupon.delete');
		$router->get('grouponItemList/{id}', 'MultiGrouponController@grouponItemList')->name('admin.promotion.multiGroupon.grouponItemList');
		$router->get('getRefundModal', 'MultiGrouponController@getRefundModal')->name('admin.promotion.multiGroupon.getRefundModal');
		$router->get('getRefundItemsPaginate', 'MultiGrouponController@getRefundItemsPaginate')->name('admin.promotion.multiGroupon.getRefundItemsPaginate');
		$router->get('getRefundList', 'MultiGrouponController@getRefundList')->name('admin.promotion.multiGroupon.getRefundList');
	});

	// 新人礼
	$router->group(['prefix' => 'gift/new_user'], function () use ($router) {
		$router->get('/', 'GiftNewUserController@index')->name('admin.promotion.gift.new.user.index');
		$router->get('/create', 'GiftNewUserController@create')->name('admin.promotion.gift.new.user.create');
		$router->post('/store', 'GiftNewUserController@store')->name('admin.promotion.gift.new.user.store');
		$router->get('/api/coupon', 'GiftNewUserController@coupon_api')->name('admin.promotion.gift.new.user.api.coupon');
		$router->get('/{id}/edit', 'GiftNewUserController@edit')->name('admin.promotion.gift.new.user.api.edit');
		$router->post('/{id}/update', 'GiftNewUserController@update')->name('admin.promotion.gift.new.user.api.update');
		$router->post('/toggleStatus', 'GiftNewUserController@toggleStatus')->name('admin.promotion.gift.new.user.toggleStatus');
		$router->post('/delete', 'GiftNewUserController@destroy')->name('admin.promotion.gift.new.user.delete');
		$router->get('/{id}/edit', 'GiftNewUserController@edit')->name('admin.promotion.gift.new.user.edit');
	});

	// 生日礼
	$router->group(['prefix' => 'gift/birthday'], function () use ($router) {
		$router->get('/', 'GiftBirthdayController@index')->name('admin.promotion.gift.birthday.index');
		$router->get('/user', 'GiftBirthdayController@user')->name('admin.promotion.gift.birthday.user');
		$router->get('/create', 'GiftBirthdayController@create')->name('admin.promotion.gift.birthday.create');
		$router->post('/store', 'GiftBirthdayController@store')->name('admin.promotion.gift.birthday.store');
		$router->get('/api/coupon', 'GiftBirthdayController@coupon_api')->name('admin.promotion.gift.birthday.api.coupon');
		$router->get('/{id}/edit', 'GiftBirthdayController@edit')->name('admin.promotion.gift.birthday.api.edit');
		$router->post('/{id}/update', 'GiftBirthdayController@update')->name('admin.promotion.gift.birthday.api.update');
		$router->post('/toggleStatus', 'GiftBirthdayController@toggleStatus')->name('admin.promotion.gift.birthday.toggleStatus');
		$router->post('/delete', 'GiftBirthdayController@destroy')->name('admin.promotion.gift.birthday.delete');
		$router->get('/{id}/edit', 'GiftBirthdayController@edit')->name('admin.promotion.gift.birthday.edit');
	});

	//砍价
	$router->group(['prefix' => 'reduce'], function () use ($router) {
		$router->get('/', 'ReduceController@index')->name('admin.promotion.reduce.index');
		$router->get('create', 'ReduceController@create')->name('admin.promotion.reduce.create');
		$router->get('edit/{id}', 'ReduceController@edit')->name('admin.promotion.reduce.edit');
		$router->post('delete/{id}', 'ReduceController@delete')->name('admin.promotion.reduce.delete');
		$router->post('store', 'ReduceController@store')->name('admin.promotion.reduce.store');
		$router->get('getSpuModal', 'ReduceController@getSpuModal')->name('admin.promotion.reduce.getSpuModal');
		$router->post('getSpuData', 'ReduceController@getSpuData')->name('admin.promotion.reduce.getSpuData');
		$router->post('update', 'ReduceController@update')->name('admin.promotion.reduce.update');

		$router->get('getItemLists', 'ReduceController@getItemLists')->name('admin.promotion.reduce.getItemLists');
		$router->get('getUserLists', 'ReduceController@getUserLists')->name('admin.promotion.reduce.getUserLists');
		$router->get('getHelpTextModal', 'ReduceController@getHelpTextModal')->name('admin.promotion.reduce.getHelpTextModal');
		$router->post('settings', 'ReduceController@settings')->name('admin.promotion.reduce.settings');
	});

	//秒杀
	$router->group(['prefix' => 'seckill'], function () use ($router) {
		$router->get('/', 'SeckillController@index')->name('admin.promotion.seckill.index');
		$router->get('create', 'SeckillController@create')->name('admin.promotion.seckill.create');
		$router->get('edit/{id}', 'SeckillController@edit')->name('admin.promotion.seckill.edit');

		$router->get('getSpu', 'SeckillController@getSpu')->name('admin.promotion.seckill.getSpu');
		$router->post('getSpuData', 'SeckillController@getSpuData')->name('admin.promotion.seckill.getSpuData');
		$router->get('getSelectGoods', 'SeckillController@getSelectGoods')->name('admin.promotion.seckill.getSelectGoods');

		$router->post('store', 'SeckillController@store')->name('admin.promotion.seckill.store');
		$router->post('updateDisable', 'SeckillController@updateDisable')->name('admin.promotion.seckill.updateDisable');
		$router->post('update', 'SeckillController@update')->name('admin.promotion.seckill.update');
		$router->post('delete/{id}', 'SeckillController@delete')->name('admin.promotion.seckill.delete');
	});

	//套餐
	$router->group(['prefix' => 'suit'], function () use ($router) {
		$router->get('/', 'SuitController@index')->name('admin.promotion.suit.index');
		$router->get('create', 'SuitController@create')->name('admin.promotion.suit.create');
		$router->post('toggleStatus', 'SuitController@toggleSuitStatus')->name('admin.suit.toggle.suit.status');
		$router->post('store', 'SuitController@store')->name('admin.promotion.suit.store');

		$router->get('edit/{id}', 'SuitController@edit')->name('admin.promotion.suit.edit');
		$router->post('delete', 'SuitController@destroy')->name('admin.promotion.suit.delete');
		//套餐item
		$router->get('create/{id}/add', 'SuitController@createItem')->name('admin.promotion.suit.create.item');
		$router->get('item/{id}/edit', 'SuitController@editItem')->name('admin.promotion.suit.create.item.edit');
		$router->get('show/{id}/item', 'SuitController@ShowItem')->name('admin.promotion.suit.ShowItem');
		$router->post('store/item/add', 'SuitController@storeItem')->name('admin.promotion.suit.store.item');
		$router->post('getGoodsInfo', 'SuitController@getGoodsInfo')->name('admin.promotion.suit.getGoodsInfo');
		$router->post('item/toggleStatus', 'SuitController@toggleSuitItemStatus')->name('admin.suit.toggle.suit.item.status');
		$router->post('delete/item', 'SuitController@destroyItem')->name('admin.promotion.suit.delete.item');
		$router->post('item/{id}/update', 'SuitController@updateItem')->name('admin.promotion.suit.store.item.update');
	});
});

//物流管理
$router->group(['prefix' => 'admin/store/shippingmethod'], function () use ($router) {
	$router->get('company', 'ShippingMethodController@company')->name('admin.shippingmethod.company');
	$router->get('Create', 'ShippingMethodController@CompanyCreate')->name('admin.shippingmethod.CompanyCreate');
	$router->post('companyStore', 'ShippingMethodController@companyStore')->name('admin.shippingmethod.companyStore');
	$router->post('delcompany/{id}', 'ShippingMethodController@deletedCompany')->name('admin.shippingmethod.deletedCompany');
});

//订单
$router->group(['prefix' => 'admin/store/order'], function () use ($router) {
	$router->get('/', 'OrdersController@index')->name('admin.orders.index');
	$router->get('detail/{id}', 'OrdersController@show')->name('admin.orders.show');
	$router->get('import/orders', 'OrdersController@ordersImport')->name('admin.orders.import');
	$router->post('import/order_send', 'OrdersController@importOrderSend')->name('admin.orders.saveimport');
	$router->get('deliver/{id}', 'OrdersController@ordersDeliver')->name('admin.orders.deliver');
	$router->get('deliver/{id}/edit', 'OrdersController@ordersDeliverEdit')->name('admin.orders.deliver.edit');
	$router->get('multiple_deliver', 'OrdersController@ordersMultipleDeliver')->name('admin.orders.multiple.deliver');
	$router->post('doDeliver', 'OrdersController@deliver')->name('admin.orders.savedeliver');
	$router->post('close/{id}', 'OrdersController@close')->name('admin.orders.close');
	$router->get('export/job', 'OrdersController@exportJob')->name('admin.orders.export.job');
	$router->get('export/getExportData', 'OrdersController@getExportData')->name('admin.orders.getExportData');
	$router->get('editAddress/{id}', 'OrdersController@editAddress')->name('admin.orders.editAddress');
	$router->post('postAddress', 'OrdersController@postAddress')->name('admin.orders.postAddress');
});

//退换货
$router->group(['prefix' => 'admin/store/refund'], function () use ($router) {
	$router->get('/', 'RefundController@index')->name('admin.refund.index');
	$router->get('show/{id}', 'RefundController@show')->name('admin.refund.show');
	$router->post('store', 'RefundController@store')->name('admin.refund.store');
	$router->post('paid', 'RefundController@paid')->name('admin.refund.paid');
	$router->get('getStatus/{id}', 'RefundController@getStatus')->name('admin.refund.getStatus');
	$router->post('changeStatus', 'RefundController@changeStatus')->name('admin.refund.changeStatus');
	$router->get('getExportData', 'RefundController@getExportData')->name('admin.refund.getExportData');
});

//评论
$router->group(['prefix' => 'admin/store/comments'], function () use ($router) {
	$router->get('/', 'CommentsController@index')->name('admin.comments.index');
	$router->get('edit/{id}', 'CommentsController@edit')->name('admin.comments.edit');
	$router->post('update/{id}', 'CommentsController@update')->name('admin.comments.update');
	$router->get('create', 'CommentsController@create')->name('admin.comments.create');
	$router->post('searchGoods', 'CommentsController@searchGoods')->name('admin.comments.searchGoods');
	$router->post('searchUsers', 'CommentsController@searchUsers')->name('admin.comments.searchUsers');
	$router->post('store', 'CommentsController@store')->name('admin.comments.store');
});

$router->group(['prefix' => 'admin/store/article'], function () use ($router) {
	$router->get('/', 'ArticleController@index')->name('admin.store.article.index');
	$router->get('create', 'ArticleController@create')->name('admin.store.article.create');
	$router->get('edit/{id}', 'ArticleController@edit')->name('admin.store.article.edit');
	$router->get('delete/{id}', 'ArticleController@delete')->name('admin.store.article.delete');
	$router->post('status', 'ArticleController@status')->name('admin.store.article.status');
	$router->post('store', 'ArticleController@store')->name('admin.store.article.store');
	$router->get('getSpu', 'ArticleController@getSpu')->name('admin.store.article.getSpu');
	$router->post('getSpuData', 'ArticleController@getSpuData')->name('admin.store.article.getSpuData');
});

$router->group(['prefix' => 'admin/store/setting'], function ($router) {
	$router->get('user/agreement', 'SettingsController@agreement')->name('admin.store.user.agreement');
	$router->post('agreement/save', 'SettingsController@saveAgreement')->name('admin.store.agreement.save');
});

/**
 * 积分商城
 */
$router->group(['prefix' => 'admin/store/point-mall', 'namespace' => 'PointMall'], function () use ($router) {
	$router->group(['prefix' => 'goods'], function () use ($router) {
		$router->get('/', 'GoodsController@index')->name('admin.point-mall.goods.index');
		$router->get('create', 'GoodsController@create')->name('admin.point-mall.goods.create');
		$router->get('edit/{id}', 'GoodsController@edit')->name('admin.point-mall.goods.edit');
	});

	$router->group(['prefix' => 'orders'], function () use ($router) {
		$router->get('/', 'OrdersController@index')->name('admin.point-mall.orders.index');
		$router->get('show/{id}', 'OrdersController@show')->name('admin.point-mall.orders.show');
		$router->get('getExportData', 'OrdersController@getExportData')->name('admin.point-mall.orders.getExportData');
	});
});