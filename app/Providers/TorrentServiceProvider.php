<?php namespace Lighthouse\Providers;

use Elastica\Client;
use Illuminate\Support\ServiceProvider;

class TorrentServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// TODO Ustawienie indeksu
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        $elastica = new Client();

        $this->app->singleton(
            'Lighthouse\Services\Torrents\Contracts\Service', 'Lighthouse\Services\Torrents\Service');
        $this->app->singleton(
            'Lighthouse\Services\Torrents\Contracts\Repository', 'Lighthouse\Services\Torrents\Repositories\ElasticSearch');
        $this->app->singleton(
            'Lighthouse\Services\Torrents\Contracts\Mapper', 'Lighthouse\Services\Torrents\Mappers\KickassExportData');
        $this->app->when('Lighthouse\Services\Torrents\Repositories\ElasticSearch')
            ->needs('Elastica\Type')
            ->give(function() use($elastica) {
                return $elastica
                    ->getIndex('lighthouse')
                    ->getType('torrent');
            });
	}

}
