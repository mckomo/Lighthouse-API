<?php

namespace App\Providers;

use Elastica;
use Predis;
use Illuminate\Support\ServiceProvider;

class LighthouseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $elastica = new Elastica\Client([
            'host' => 'elasticsearch'
        ]);
        $predis = new Predis\Client(
            ['host' => 'redis'],
            ['prefix' => 'torrent:']
        );

        $this->app->instance('Elastica\Client', $elastica);
//        $this->app->instance('Predis\Client', $predis);
        $this->app->singleton(
            'Lighthouse\Core\ServiceInterface', 'Lighthouse\Service');
        $this->app->singleton(
            'Lighthouse\Core\RepositoryInterface', 'Lighthouse\Repositories\ElasticsearchRepository');
        $this->app->singleton(
            'Lighthouse\Mapper', 'Lighthouse\Mappers\KickassExportData');
        $this->app
            ->when('Lighthouse\Repositories\ElasticsearchRepository')
            ->needs('Elastica\Type')
            ->give(function () use ($elastica) {
                return $elastica
                    ->getIndex('lighthouse')
                    ->getType('torrent');
            });
    }
}
