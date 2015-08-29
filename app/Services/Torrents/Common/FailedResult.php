<?php

namespace Lighthouse\Services\Torrents\Common;

use Lighthouse\Services\Torrents\Entities\Error;

class FailedResult extends OperationResult
{
    private $error;

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * @param Error $error
     */
    public function withError(Error $error)
    {
        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }
}
