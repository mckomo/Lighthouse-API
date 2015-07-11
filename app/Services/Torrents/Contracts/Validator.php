<?php namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Entities\Base as Entity;

interface Validator
{
    /**
     * @param Entity $entity
     * @param array $errors
     * @return bool
     */
    public function isValid(Entity $entity, array &$errors);

}