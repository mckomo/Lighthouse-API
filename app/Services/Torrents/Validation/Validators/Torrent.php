<?php

namespace Lighthouse\Services\Torrents\Validation\Validators;

use Lighthouse\Services\Torrents\Common\ErrorMessages;
use Lighthouse\Services\Torrents\Common\Utils\ValidationHelper;
use Lighthouse\Services\Torrents\Contracts\Validator;
use Lighthouse\Services\Torrents\Entities\Base as Entity;

class Torrent implements Validator
{
    public function isValid(Entity $entity = null, array &$errorMessages = null)
    {
        if (is_null($entity)) {
            $errorMessages[] = ErrorMessages::EmptyResource;

            return false;
        }

        if (!ValidationHelper::isHash($entity->hash)) {
            $errorMessages[] = ErrorMessages::InvalidHash;
        }

        if (!ValidationHelper::isLongerThan($entity->name, 0)) {
            $errorMessages[] = ErrorMessages::EmptyName;
        }

        if (!ValidationHelper::isValidFilename($entity->filename)) {
            $errorMessages[] = ErrorMessages::InvalidFilename;
        }

        if (!ValidationHelper::isValidUtf8($entity->name)) {
            $errorMessages[] = ErrorMessages::InvalidEncodedName;
        }

        if (!ValidationHelper::isLongerThan($entity->category, 0)) {
            $errorMessages[] = ErrorMessages::EmptyCategory;
        }

        if (!ValidationHelper::isPositiveInteger($entity->size)) {
            $errorMessages[] = ErrorMessages::NonpositiveSize;
        }

        if (!ValidationHelper::isUrl($entity->url)) {
            $errorMessages[] = ErrorMessages::InvalidUrl;
        }

        if (!ValidationHelper::isMagnetLink($entity->magnetLink)) {
            $errorMessages[] = ErrorMessages::InvalidUrl;
        }

        if (!ValidationHelper::isIso8601Utc($entity->uploadedAt)) {
            $errorMessages[] = ErrorMessages::UploadTimeFormat;
        }

        if (ValidationHelper::isNegativeInteger($entity->seedCount)) {
            $errorMessages[] = ErrorMessages::NegativePeerCount;
        }

        if (ValidationHelper::isNegativeInteger($entity->peerCount)) {
            $errorMessages[] = ErrorMessages::NegativeSeedCount;
        }

        return count($errorMessages) == 0;
    }
}
