<?php

namespace Lighthouse\Core;

interface ValidatorInterface
{
    /**
     * @param Entity $entity
     * @param array  $errorMessages
     *
     * @return bool
     */
    public function isValid(Entity $entity, array &$errorMessages);
}
