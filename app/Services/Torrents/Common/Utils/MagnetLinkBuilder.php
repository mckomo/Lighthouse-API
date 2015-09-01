<?php

namespace Lighthouse\Services\Torrents\Common\Utils;

class MagnetLinkBuilder
{
    const MagnetLinkTemplate = 'magnet:?xt=urn:btih:{hash}&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80';

    /**
     * @param string $hash
     *
     * @return string
     */
    public static function build($hash)
    {
        return str_replace('{hash}', $hash, static::MagnetLinkTemplate);
    }
}
