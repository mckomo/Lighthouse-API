<?php

namespace Lighthouse\Services\Torrents\Validation\Validators;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Contracts\Validator;
use Lighthouse\Services\Torrents\Entities\Base as Entity;
use Lighthouse\Services\Torrents\Validation\Utils\ValidationHelper;

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
    public function isValid(Entity $query = null, array &$errors = null)
    {
        if (is_null($query)) {
            $errors[] = ErrorMessages::EmptyResource;

            return false;
        }

        if (!ValidationHelper::isLongerThan($query->phrase, 2)) {
            $errors[] = ErrorMessages::ShortPhrase;
        }

        if (!is_null($query->size) && !ValidationHelper::isInRange($query->size, 1, 100)) {
            $errors[] = ErrorMessages::OutOfRangeLimit;
        }

        if (!is_null($query->category) && !in_array($query->category, $this->supportedCategories)) {
            $errors[] = ErrorMessages::UnsupportedCategory;
        }

        return count($errors) == 0;
    }
}
