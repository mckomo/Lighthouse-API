<?php

namespace Lighthouse\Services\Torrents\Common\Utils;

use Illuminate\Support\Str;

class UnitHelper
{
    const MagnetLinkTemplate = 'magnet:?xt=urn:btih:{hash}&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80';

    /**
     * @param string $torrentHash
     *
     * @return string
     */
    public static function buildMagnetLink($torrentHash)
    {
        return str_replace('{hash}', $torrentHash, static::MagnetLinkTemplate);
    }

    /**
     * @param string $name
     * @param string $fileExtension
     *
     * @return string
     */
    public static function formatFilename($name, $fileExtension = '')
    {
        $filename = Str::slug($name);

        if (ValidationHelper::isLongerThan($fileExtension, 0)) {
            $filename = "{$filename}.{$fileExtension}";
        }

        return $filename;
    }
}
