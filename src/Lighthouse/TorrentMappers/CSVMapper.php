<?php

namespace Lighthouse\TorrentMappers;

use Lighthouse\Core\TorrentMapperInterface;
use Lighthouse\Torrent;

/**
 * Class CSVMapper
 *
 * Mapper for torrents stored as CSV string
 * Mapper is compatible with KickassTorrents CSV database dump
 * Supported CSV format:
 * INFO_HASH|NAME|CATEGORY|X|X|SIZE_IN_BYTES|X|X|SEED_COUNT|PEER_COUNT|UPLOAD_TIMESTAMP
 */
final class CSVMapper implements TorrentMapperInterface
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
            'category'      => strtolower($data[2]),
            'size'          => intval($data[5]),
            'uploadedAt'    => gmdate('Y-m-d\TH:i:s\Z', intval($data[10])),
            'seedCount'     => intval($data[8]),
            'peerCount'     => intval($data[9]),
        ]);
    }
}
