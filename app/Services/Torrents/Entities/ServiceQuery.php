<?php

namespace Lighthouse\Services\Torrents\Entities;

class ServiceQuery extends Base
{
    /**
     * @var string
     */
    public $phrase;

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $category;

    /**
     * @var int
     */
    public $sortBy;

    /**
     * @var string
     */
    public $sortOrder;
}
