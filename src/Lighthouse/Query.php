<?php

namespace Lighthouse;

use Lighthouse\Core\Entity;

class Query extends Entity
{
    /**
     * @var string
     */
    public $phrase;

    /**
     * @var int
     */
    public $limit;

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
