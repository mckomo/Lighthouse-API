<?php

namespace App\Providers;

use Elastica;
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
        $this->app->singleton(
            'Lighthouse\Core\ServiceInterface', 'Lighthouse\Service');
        $this->app->singleton(
            'Lighthouse\Core\RepositoryInterface', 'Lighthouse\Repositories\ElasticsearchRepository');
        $this->app->singleton(
            'Lighthouse\Mapper', 'Lighthouse\Mappers\KickassExportData');
        $this->app->singleton(
            'Lighthouse\Core\TorrentMapperInterface', 'Lighthouse\TorrentMappers\KickassMapper');

        $this->registerElasticsearch();
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
}
