<?php

namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Common\OperationResult;
use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;

interface Service
{
    /**
     * @param $phrase
     * @param array $options
     *
     * @return OperationResult
     */
    public function search(Query $query);

    /**
     * @param Torrent $torrent
     *
     * @return OperationResult
     */
    public function upload(Torrent $torrent);

    /**
     * @param $hash
     *
     * @return OperationResult
     */
    public function get($hash);
}
