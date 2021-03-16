<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce;

use Event;
use GuoJiangClub\Component\Reduce\Schedule\ReduceSchedule;
use Illuminate\Support\ServiceProvider;

class ReduceServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        'GuoJiangClub\Component\Reduce\Listeners\ReduceEventListener',
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (!class_exists('CreateReduceTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_reduce_tables.php.stub' => database_path()."/migrations/{$timestamp}_create_reduce_tables.php",
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
        $this->app->make('iBrand\Scheduling\ScheduleList')->add(ReduceSchedule::class);
    }
}
