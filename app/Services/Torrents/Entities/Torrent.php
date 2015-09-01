<?php

namespace Lighthouse\Services\Torrents\Entities;

use Lighthouse\Services\Torrents\Common\Utils\MagnetLinkBuilder;

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
    public $magnetLink;

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

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (is_null($this->magnetLink)) {
            $this->magnetLink = MagnetLinkBuilder::build($this->hash);
        }
    }
}
