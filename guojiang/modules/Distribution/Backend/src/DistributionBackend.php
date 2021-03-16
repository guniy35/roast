<?php

namespace GuoJiangClub\Distribution\Backend;

use GuoJiangClub\Distribution\Backend\Seeds\DistributionBackendTablesSeeder;
use Encore\Admin\Admin;
use Encore\Admin\Extension;
use Illuminate\Support\Facades\Artisan;

class DistributionBackend extends Extension
{
	/**
	 * Bootstrap this package.
	 */
	public static function boot()
	{
		Admin::extend('ibrand-distribution-backend', __CLASS__);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function import()
	{
		Artisan::call('db:seed', ['--class' => DistributionBackendTablesSeeder::class]);
	}
}
