<?php

namespace App\Providers;

use Elastica;
use Illuminate\Support\ServiceProvider;
use Predis\Client as Predis;

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
        $this->app
            ->when('Lighthouse\CachedService')
            ->needs('Lighthouse\Core\ServiceInterface')
            ->give('Lighthouse\Service');

        $this->app->singleton(
            'Lighthouse\Core\ServiceInterface', 'Lighthouse\CachedService');
        $this->app->singleton(
            'Lighthouse\Core\RepositoryInterface', 'Lighthouse\ElasticsearchRepository');
        $this->app->singleton(
            'Lighthouse\Core\StorageInterface', 'Lighthouse\RedisStorage');
        $this->app->singleton(
            'Lighthouse\Core\TorrentMapperInterface', 'Lighthouse\TorrentMappers\KickassMapper');

        $this->registerElasticsearch();
        $this->registerRedis();
    }

    private function registerElasticsearch()
    {
        // TODO Move settings to config file
        $client = new Elastica\Client(
            ['host' => 'elasticsearch']
        );

        $index = $client->getIndex('lighthouse');
        $type = $index->getType('torrent');

        $this->app->instance('Elastica\Index', $index);
        $this->app->instance('Elastica\Type', $type);
    }

    private function registerRedis()
    {
        // TODO Move settings to config file
        $predis = new Predis(['host' => 'redis'], ['prefix' => 'torrent:']);

        $this->app->instance(
            'Predis\Client', $predis);
    }
}
