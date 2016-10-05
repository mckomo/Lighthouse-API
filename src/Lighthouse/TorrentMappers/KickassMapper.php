<?php

namespace Lighthouse\TorrentMappers;

use Lighthouse\Core\TorrentMapperInterface;
use Lighthouse\Torrent;

/**
 * Class KickassMapper.
 *
 * Mapper for KickassTorrents CSV torrent dump
 */
final class KickassMapper implements TorrentMapperInterface
{
    const REQUIRED_FIELD_COUNT = 11;

    /**
     * @param string $line
     *
     * @return Torrent
     */
    public function map($csv)
    {
        $data = explode('|', $csv);

        if (count($data) < static::REQUIRED_FIELD_COUNT) {
            return;
        }

        return new Torrent([
            'infoHash'      => $data[0],
            'name'          => $data[1],
            'category'      => $data[2],
            'size'          => intval($data[5]),
            'url'           => $data[4],
            'uploadedAt'    => gmdate('Y-m-d\TH:i:s\Z', intval($data[10])),
            'seedCount'     => intval($data[8]),
            'peerCount'     => intval($data[9]),
        ]);
    }
}
