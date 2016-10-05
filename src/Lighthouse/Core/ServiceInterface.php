<?php

namespace Lighthouse\Core;

use Lighthouse\Query;
use Lighthouse\Result;
use Lighthouse\Torrent;

interface ServiceInterface
{
    /**
     * @param $infoInfoHash
     *
     * @return Result
     */
    public function get($infoInfoHash);

    /**
     * @param $phrase
     * @param array $options
     *
     * @return Result
     */
    public function search(Query $query);

    /**
     * @param Torrent $torrent
     *
     * @return Result
     */
    public function put(Torrent $torrent);
}
