<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Console;

use GuoJiangClub\EC\Open\Backend\Member\Seeds\MemberBackendTablesSeeder;
use GuoJiangClub\EC\Open\Backend\Store\Seeds\StoreBackendTablesSeeder;
use GuoJiangClub\Distribution\Backend\Seeds\DistributionBackendTablesSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'ibrand:store-install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'install ibrand\'s store backend system.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->call('ibrand:backend-install');
		$this->call('ibrand:backend-install-extensions');
		$this->call('ibrand:store-default-value');
		$this->call('ibrand:store-default-specs');
		$this->call('db:seed', ['--class' => MemberBackendTablesSeeder::class]);
		$this->call('db:seed', ['--class' => StoreBackendTablesSeeder::class]);
		$this->call('import:add-article-menus');
		$this->call('import:goods-limit-menus');
		$this->call('import:point-mall-menus');
		$this->call('import:seckill-menus');
		$this->call('db:seed', ['--class' => DistributionBackendTablesSeeder::class]);
	}
}