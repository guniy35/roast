<?php


$router->get('demo/test/repository','TestController@testRepository');

$router->get('demo/test/model','TestController@testModel');

$router->get('store/list', 'TestController@goodsList');