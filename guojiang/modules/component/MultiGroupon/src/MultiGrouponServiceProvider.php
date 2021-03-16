<?php

/*
 * This file is part of ibrand/multi-groupon.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\MultiGroupon;

use Event;
use GuoJiangClub\Component\MultiGroupon\Schedule\MultiItemSchedule;
use GuoJiangClub\Component\MultiGroupon\Schedule\PromotionSchedule;
use Illuminate\Support\ServiceProvider;

class MultiGrouponServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        'GuoJiangClub\Component\MultiGroupon\Listeners\MultiGrouponEventListener',
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (!class_exists('CreateMultiGrouponTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_multi_groupon_tables.php.stub' => database_path()."/migrations/{$timestamp}_create_multi_groupon_tables.php",
            ], 'migrations');
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->make('iBrand\Scheduling\ScheduleList')->add(MultiItemSchedule::class);

        $this->app->make('iBrand\Scheduling\ScheduleList')->add(PromotionSchedule::class);
    }
}
