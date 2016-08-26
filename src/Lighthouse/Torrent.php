<?php

namespace Lighthouse;

use Lighthouse\Core\Entity;
use Lighthouse\Utils\UnitHelper;

class Torrent extends Entity
{
    /**
     * @var string
     */
    public $infoHash;

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

        if (is_null($this->magnetLink) && $this->infoHash) {
            $this->magnetLink = UnitHelper::buildMagnetLink($this->infoHash);
        }

        if (is_null($this->filename) && $this->name) {
            $this->filename = UnitHelper::formatFilename($this->name, 'torrent');
        }
    }
}
