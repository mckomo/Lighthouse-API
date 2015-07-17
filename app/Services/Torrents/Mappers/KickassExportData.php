<?php namespace Lighthouse\Services\Torrents\Mappers;

use Lighthouse\Services\Torrents\Contracts\Mapper;
use Lighthouse\Services\Torrents\Entities\Torrent;

final class KickassExportData implements Mapper
{

    const RequiredFieldCount = 11;

    /**
     * @param string $line
     * @return Torrent
     */
    public function map($line)
    {
        $data = explode('|', $line);

        if (count($data) < static::RequiredFieldCount)
            return null;

        return new Torrent([
            'hash'          => $data[0],
            'name'          => $data[1],
            'category'      => $data[2],
            'size'          => intval($data[5]),
            'url'           => $data[4],
            'uploadedAt'    => gmdate('Y-m-d\TH:i:s\Z', intval($data[10])),
            'seedCount'     => intval($data[8]),
            'peerCount'     => intval($data[9])
        ]);
    }
}
