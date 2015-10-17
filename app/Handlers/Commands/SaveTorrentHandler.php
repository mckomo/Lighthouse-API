<?php

namespace Lighthouse\Handlers\Commands;

use Lighthouse\Commands\SaveTorrent;
use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Service;

class SaveTorrentHandler
{
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
     * @param SaveTorrent $command
     *
     * @return OperationResult
     */
    public function handle(SaveTorrent $command)
    {
        return $this->service->save($command->torrent);
    }
}
