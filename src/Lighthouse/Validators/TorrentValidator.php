<?php

namespace Lighthouse\Validators;

use Lighthouse\Common\ErrorMessages;
use Lighthouse\Core\Entity;
use Lighthouse\Core\ValidatorInterface;
use Lighthouse\Utils\ValidationHelper;

class TorrentValidator implements ValidatorInterface
{
    public function isValid(Entity $entity = null, array &$errorMessages = null)
    {
        if (is_null($entity)) {
            $errorMessages[] = ErrorMessages::EmptyResource;

            return false;
        }

        if (!ValidationHelper::isHash($entity->infoHash)) {
            $errorMessages[] = ErrorMessages::InvalidInfoHash;
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
