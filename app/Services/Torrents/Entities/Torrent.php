<?php

namespace Lighthouse\Services\Torrents\Entities;

use Lighthouse\Services\Torrents\Common\Utils\UnitHelper;

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
    public $filename;

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

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (is_null($this->magnetLink)) {
            $this->magnetLink = UnitHelper::buildMagnetLink($this->hash);
        }

        if (is_null($this->filename)) {
            $this->filename = UnitHelper::formatFilename($this->name, 'torrent');
        }
    }
}
