<?php namespace Lighthouse\Handlers\Commands;

use Lighthouse\Commands\UploadTorrent as UploadCommand;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Service;

class UploadTorrentHandler {

    protected $service;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Service $service)
	{
		$this->service = $service;
	}

	/**
	 * Handle the command.
	 *
	 * @param  UploadTorrentsCommand  $command
	 * @return OperationResult
	 */
	public function handle(UploadCommand $command)
	{
        return $this->service->upload($command->torrent);
	}

}
