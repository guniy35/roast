<?php
$router->get('/', 'DashboardController@index')->name('admin.distribution.index');
$router->get('getDashBoardData', 'DashboardController@getDashBoardData')->name('admin.distribution.getDashBoardData');

$router->get('dataStatistics', 'DashboardController@dataStatistics')->name('admin.distribution.dataStatistics');
$router->get('monthDataStatistics', 'DashboardController@monthDataStatistics')->name('admin.distribution.monthDataStatistics');

$router->group(['prefix' => 'setting'], function () use ($router) {
    $router->get("sys_setting", "SettingsController@index")->name('admin.distribution.system.setting');
    $router->post('saveSetting', 'SettingsController@saveSettings')->name('admin.distribution.system.saveSetting');

    $router->group(['prefix' => 'goods'], function () use ($router) {
        $router->get("/", "SettingsController@goods")->name('admin.distribution.goods.setting');
        $router->get("sync", "SettingsController@syncGoods")->name('admin.distribution.goods.syncGoods');

        $router->post("postSyncGoods", "SettingsController@postSyncGoods")->name('admin.distribution.goods.postSyncGoods');

        $router->get("editGoods", "SettingsController@editGoods")->name('admin.distribution.goods.editGoods');
        $router->post("saveGoods", "SettingsController@saveGoods")->name('admin.distribution.goods.saveGoods');

        $router->get("editBatchGoods", "SettingsController@editBatchGoods")->name('admin.distribution.goods.editBatchGoods');
        $router->post("saveBatchGoods", "SettingsController@saveBatchGoods")->name('admin.distribution.goods.saveBatchGoods');
    });
    

});

$router->group(['prefix' => 'cash'], function () use ($router) {
    $router->get("/", "CashController@index")->name('admin.balance.cash.index');
    $router->get("{id}/show", "CashController@show")->name('admin.balance.cash.show');
    $router->get("{id}/operatePay", "CashController@operatePay")->name('admin.balance.cash.operatePay');

    $router->post("view", "CashController@review")->name('admin.balance.cash.review');
    $router->post("applyPay", "CashController@applyPay")->name('admin.balance.cash.applyPay');

    $router->get('export/getExportData', 'CashController@getExportData')->name('admin.balance.cash.getExportData');
    
});


$router->group(['prefix' => 'agent'], function () use ($router) {
    $router->get("/", "AgentController@index")->name('admin.distribution.agent.index');
    $router->get("{id}/agentUsers", "AgentController@agentUsers")->name('admin.distribution.agent.agentUsers');
    $router->get("{id}/audit", "AgentController@audit")->name('admin.distribution.agent.audit');
    $router->get("{id}/edit", "AgentController@edit")->name('admin.distribution.agent.edit');
    $router->get('export/getExportData', 'AgentController@getExportData')->name('admin.distribution.agent.getExportData');
    $router->get("subAgent", "AgentController@subAgent")->name('admin.distribution.agent.subAgent');

    $router->post("saveAgent", "AgentController@saveAgent")->name('admin.distribution.agent.saveAgent');
    $router->post("retreatAgent", "AgentController@retreatAgent")->name('admin.distribution.agent.retreatAgent');
    $router->post("restoreAgent", "AgentController@restoreAgent")->name('admin.distribution.agent.restoreAgent');

    $router->get("create", "AgentController@create")->name('admin.distribution.agent.create');
    $router->post("store", "AgentController@store")->name('admin.distribution.agent.store');
    $router->get("searchUser", "AgentController@searchUser")->name('admin.distribution.agent.searchUser');

    $router->group(['prefix' => 'orders'], function () use ($router) {
        $router->get("/", "AgentOrderController@index")->name('admin.distribution.agent.orders.index');
        $router->get("{id}/show", "AgentOrderController@show")->name('admin.distribution.agent.orders.show');
    });

    $router->get("commission", "CommissionController@index")->name('admin.distribution.agent.commission.index');

    $router->get("addAgentOrder/{id}", "AgentOrderController@addAgentOrder")->name('admin.distribution.agent.orders.addAgentOrder');
    $router->post("postAgentOrder", "AgentOrderController@postAgentOrder")->name('admin.distribution.agent.orders.postAgentOrder');

    
});

$router->group(['prefix' => 'orders'], function () use ($router) {
    $router->get("/", "OrdersController@index")->name('admin.distribution.orders.index');
    $router->get("{id}/show", "OrdersController@show")->name('admin.distribution.orders.show');

    $router->get('export/getExportData', 'OrdersController@getExportData')->name('admin.distribution.orders.getExportData');
    
});

