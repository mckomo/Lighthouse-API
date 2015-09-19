<?php

namespace Lighthouse\Services\Torrents\Validation\Validators;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Contracts\Validator;
use Lighthouse\Services\Torrents\Entities\Base as Entity;
use Lighthouse\Services\Torrents\Common\Utils\ValidationHelper;

class Query implements Validator
{
    protected $supportedCategories = [
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

        if (!is_null($query->size) && !ValidationHelper::isInRange($query->size, 1, 100)) {
            $errorMessages[] = ErrorMessages::OutOfRangeLimit;
        }

        if (!is_null($query->category) && !in_array($query->category, $this->supportedCategories)) {
            $errorMessages[] = ErrorMessages::UnsupportedCategory;
        }

        return count($errorMessages) == 0;
    }
}
