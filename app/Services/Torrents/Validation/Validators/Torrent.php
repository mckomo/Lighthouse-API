<?php

namespace Lighthouse\Services\Torrents\Validation\Validators;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Contracts\Validator;
use Lighthouse\Services\Torrents\Entities\Base as Entity;
use Lighthouse\Services\Torrents\Validation\Utils\ValidationHelper;

class Torrent implements Validator
{
    public function isValid(Entity $entity = null, array &$errors = null)
    {
        if (is_null($entity)) {
            $errors[] = ErrorMessages::EmptyResource;

            return false;
        }

        if (!ValidationHelper::isHash($entity->hash)) {
            $errors[] = ErrorMessages::InvalidHash;
        }

        if (!ValidationHelper::isLongerThan($entity->name, 0)) {
            $errors[] = ErrorMessages::EmptyName;
        }

        if (!ValidationHelper::isValidUtf8($entity->name)) {
            $errors[] = ErrorMessages::InvalidEncodedName;
        }

        if (!ValidationHelper::isLongerThan($entity->category, 0)) {
            $errors[] = ErrorMessages::EmptyCategory;
        }

        if (!ValidationHelper::isPositiveInteger($entity->size)) {
            $errors[] = ErrorMessages::NonpositiveSize;
        }

        if (!ValidationHelper::isUrl($entity->url)) {
            $errors[] = ErrorMessages::InvalidUrl;
        }

        if (!ValidationHelper::isMagnetLink($entity->magnetLink)) {
            $errors[] = ErrorMessages::InvalidUrl;
        }

        if (!ValidationHelper::isIso8601Utc($entity->uploadedAt)) {
            $errors[] = ErrorMessages::UploadTimeFormat;
        }

        if (ValidationHelper::isNegativeInteger($entity->seedCount)) {
            $errors[] = ErrorMessages::NegativePeerCount;
        }

        if (ValidationHelper::isNegativeInteger($entity->peerCount)) {
            $errors[] = ErrorMessages::NegativeSeedCount;
        }

        return count($errors) == 0;
    }
}
