<?php

namespace Lighthouse\Validators;

use Lighthouse\Common\ErrorMessages;
use Lighthouse\Utils\ValidationHelper;
use Lighthouse\Core\Entity;
use Lighthouse\Core\ValidatorInterface;

class QueryValidator implements ValidatorInterface
{
    /**
     * @const string
     */
    const DescOrder = 'desc';

    /**
     * @const string
     */
    const AscOrder = 'asc';

    /**
     * @var array
     */
    protected static $sortableFields = [
        'peerCount',
        'seedCount',
        'size',
        'uploadedAt',
    ];

    protected static $supportedSortOrders = [
        self::DescOrder,
        self::AscOrder,
    ];

    /**
     * @var array
     */
    protected static $supportedCategories = [
        'anime',
        'applications',
        'books',
        'games',
        'movies',
        'music',
        'other',
        'tv',
        'xxx',
    ];

    /**
     * @param Entity $query
     *
     * @return bool
     */
    public function isValid(Entity $query = null, array &$errorMessages = null)
    {
        if (is_null($query)) {
            $errorMessages[] = ErrorMessages::EmptyResource;

            return false;
        }

        if (!ValidationHelper::isLongerThan($query->phrase, 2)) {
            $errorMessages[] = ErrorMessages::ShortPhrase;
        }

        if (!is_null($query->limit) && !ValidationHelper::isInRange($query->limit, 1, 100)) {
            $errorMessages[] = ErrorMessages::OutOfRangeLimit;
        }

        if (!is_null($query->category) && !in_array($query->category, static::$supportedCategories)) {
            $errorMessages[] = ErrorMessages::UnsupportedCategory;
        }

        if (!is_null($query->sortBy) && !in_array($query->sortBy, static::$sortableFields)) {
            $errorMessages[] = ErrorMessages::InvalidSortField;
        }

        if (!is_null($query->sortOrder) && !in_array($query->sortOrder, static::$supportedSortOrders)) {
            $errorMessages[] = ErrorMessages::UnsupportedSortOrder;
        }

        return empty($errorMessages);
    }
}
