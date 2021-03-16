<?php


namespace GuoJiangClub\EC\Custom\Demo;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    protected $namespace = 'GuoJiangClub\EC\Custom\Demo\Controllers';

    public function boot()
    {
        $this->mapApiRoutes();
    }

    public function register()
    {
        $this->app->bind(Goods::class, \GuoJiangClub\EC\Custom\Demo\Models\Goods::class);
        $this->app->bind(GoodsRepository::class, \GuoJiangClub\EC\Custom\Demo\Repository\GoodsRepositoryEloquent::class);
    }

    protected function mapApiRoutes()
    {
        /*Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/api.php');*/

        $api = app('Dingo\Api\Routing\Router');

        $api->version('v1',
            array_merge(config('ibrand.ec-open-api.routeAttributes'), ['namespace' => $this->namespace]), function ($router) {
                require __DIR__.'/api.php';
            });
    }
}