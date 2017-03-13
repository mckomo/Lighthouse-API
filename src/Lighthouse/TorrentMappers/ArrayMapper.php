<?php

namespace Lighthouse\TorrentMappers;

use Lighthouse\Core\TorrentMapperInterface;
use Lighthouse\Torrent;

/**
 * Class CSVMapper.
 *
 * Mapper for torrents stored as CSV string
 * Mapper is compatible with KickassTorrents CSV database dump
 * Supported CSV format:
 * INFO_HASH|NAME|CATEGORY|X|TORRENT_FILE_URL|SIZE_IN_BYTES|X|X|SEED_COUNT|PEER_COUNT|UPLOAD_TIMESTAMP
 */
final class ArrayMapper implements TorrentMapperInterface
{
    const NegativeNumber = -1;

    /**
     * @param array $data
     *
     * @return Torrent
     */
    public function map($data = [])
    {
        if (!is_array($data)) {
            return;
        }

        $torrent = new Torrent();

        $setProperty = $this->propertySetterFor($torrent)->using($data);

        $setProperty('infoHash');
        $setProperty('name');
        $setProperty('category', function ($value) {
            return strtolower($value);
        });
        $setProperty('size', function ($value) {
            return intval($value);
        });
        $setProperty('magnetLink');
        $setProperty('uploadedAt');
        $setProperty('seedCount');
        $setProperty('peerCount');

        return $torrent;
    }

    private function propertySetterFor(Torrent $torrent, $data = [])
    {
        return new class($torrent) {
            private $torrent;

            public function __construct(Torrent $torrent)
            {
                $this->torrent = $torrent;
            }

            public function using($data)
            {
                $torrent = $this->torrent;

                return function ($propertyName, $transform = null) use ($torrent, $data) {
                    if (!array_key_exists($propertyName, $data)) {
                        return;
                    }

                    $value = $data[$propertyName];

                    $torrent->$propertyName = is_callable($transform)
                        ? $transform($value)
                        : $value;
                };
            }
        };
    }
}
