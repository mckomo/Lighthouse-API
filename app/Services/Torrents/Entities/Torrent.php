<?php namespace Lighthouse\Services\Torrents\Entities;

class Torrent extends Base
{
    /**
     * @var string
     */
    public $hash;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $category;

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $uploadedAt;

    /**
     * @var int
     */
    public $seedCount;

    /**
     * @var int
     */
    public $peerCount;
}